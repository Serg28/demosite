<?php

namespace Tests\Feature\Checkout;

use App\Models\Order;
use App\Models\PaymentCredential;
use App\Models\PaymentInvoice;
use App\Models\PayMethod;
use App\Services\Payment\Gateways\NovaPay\NovaPayClient;
use App\Services\Payment\Gateways\NovaPay\NovaPayGateway;
use App\Services\Payment\WebhookProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NovaPayGatewayTest extends TestCase
{
    use RefreshDatabase;

    private const MERCHANT_ID = 'test_merchant_123';

    // Порожній ключ — sign() повертає '' (OpenSSL fail) — для тестів достатньо
    private const PRIVATE_KEY = '';

    // ────────────────────────────────────────────────────────────
    // NovaPayClient — sign
    // ────────────────────────────────────────────────────────────

    public function test_client_sign_returns_empty_string_for_invalid_key(): void
    {
        $client = $this->makeClient();
        // Пустий ключ → openssl_pkey_get_private() = false → повертає ''
        $this->assertEquals('', $client->sign('{"test":1}'));
    }

    // ────────────────────────────────────────────────────────────
    // NovaPayClient — createCheckoutSession
    // ────────────────────────────────────────────────────────────

    public function test_client_create_checkout_session_returns_session_data(): void
    {
        Http::fake([
            '*novapay*/checkout/session' => Http::response([
                'id' => 'session-abc',
                'url' => 'https://novapay.ua/checkout/session-abc',
            ], 200),
        ]);

        $client = $this->makeClient();
        $result = $client->createCheckoutSession([
            'merchant_id' => self::MERCHANT_ID,
            'callback_url' => 'https://callback.url',
            'success_url' => 'https://success.url',
            'fail_url' => 'https://fail.url',
            'delivery' => ['volume_weight' => 0.0004, 'weight' => 0.1],
        ]);

        $this->assertNotNull($result);
        $this->assertEquals('session-abc', $result['id']);
    }

    public function test_client_create_checkout_session_returns_null_on_failure(): void
    {
        Http::fake([
            '*novapay*/checkout/session' => Http::response([], 500),
        ]);

        $this->assertNull($this->makeClient()->createCheckoutSession(['merchant_id' => self::MERCHANT_ID]));
    }

    // ────────────────────────────────────────────────────────────
    // NovaPayClient — addCheckoutPayment
    // ────────────────────────────────────────────────────────────

    public function test_client_add_checkout_payment_returns_payment_url(): void
    {
        Http::fake([
            '*novapay*/checkout/payment' => Http::response([
                'url' => 'https://novapay.ua/pay/session-abc',
                'status' => 'created',
            ], 200),
        ]);

        $client = $this->makeClient();
        $result = $client->addCheckoutPayment([
            'merchant_id' => self::MERCHANT_ID,
            'session_id' => 'session-abc',
            'amount' => 150.0,
            'external_id' => 42,
            'use_hold' => false,
            'products' => [['count' => 1, 'price' => 150.0, 'description' => 'Order #42']],
        ]);

        $this->assertNotNull($result);
        $this->assertEquals('https://novapay.ua/pay/session-abc', $result['url']);
    }

    public function test_client_add_checkout_payment_returns_null_on_failure(): void
    {
        Http::fake([
            '*novapay*/checkout/payment' => Http::response([], 500),
        ]);

        $this->assertNull($this->makeClient()->addCheckoutPayment(['merchant_id' => self::MERCHANT_ID]));
    }

    // ────────────────────────────────────────────────────────────
    // NovaPayGateway — init
    // ────────────────────────────────────────────────────────────

    public function test_gateway_init_returns_payment_url_on_success(): void
    {
        Http::fake([
            '*novapay*/checkout/session' => Http::response(['id' => 'session-1'], 200),
            '*novapay*/checkout/payment' => Http::response(['url' => 'https://novapay.ua/pay/session-1'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        $result = app(NovaPayGateway::class)->init($order);

        $this->assertEquals('https://novapay.ua/pay/session-1', $result);
    }

    public function test_gateway_init_returns_empty_string_when_session_fails(): void
    {
        Http::fake([
            '*novapay*/checkout/session' => Http::response([], 500),
        ]);

        $order = $this->makeOrderWithCredentials();
        $result = app(NovaPayGateway::class)->init($order);

        $this->assertEquals('', $result);
    }

    public function test_gateway_init_returns_empty_string_when_payment_fails(): void
    {
        Http::fake([
            '*novapay*/checkout/session' => Http::response(['id' => 'session-1'], 200),
            '*novapay*/checkout/payment' => Http::response([], 500),
        ]);

        $order = $this->makeOrderWithCredentials();
        $result = app(NovaPayGateway::class)->init($order);

        $this->assertEquals('', $result);
    }

    public function test_gateway_init_passes_external_id_as_order_id(): void
    {
        Http::fake([
            '*novapay*/checkout/session' => Http::response(['id' => 'session-1'], 200),
            '*novapay*/checkout/payment' => Http::response(['url' => 'https://url'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        app(NovaPayGateway::class)->init($order);

        Http::assertSent(function ($r) use ($order) {
            $body = json_decode($r->body(), true);

            return isset($body['external_id']) && $body['external_id'] === $order->id;
        });
    }

    // ────────────────────────────────────────────────────────────
    // NovaPayGateway — status
    // ────────────────────────────────────────────────────────────

    public function test_status_returns_pending_when_no_status_in_response(): void
    {
        $invoice = new PaymentInvoice(['gateway_response' => []]);
        $this->assertEquals('pending', app(NovaPayGateway::class)->status($invoice));
    }

    #[DataProvider('novaPayStatusProvider')]
    public function test_status_maps_novapay_status_correctly(string $apiStatus, string $expected): void
    {
        $order = $this->makeOrderWithCredentials();
        $invoice = PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['status' => $apiStatus, 'session_id' => 'session-xyz'],
        ]);

        $this->assertEquals($expected, app(NovaPayGateway::class)->status($invoice));
    }

    /** @return array<string, array{string, string}> */
    public static function novaPayStatusProvider(): array
    {
        return [
            'paid' => ['paid', 'paid'],
            'processing' => ['processing', 'processing'],
            'hold' => ['hold', 'hold'],
            'expired' => ['expired', 'failed'],
            'declined' => ['declined', 'failed'],
            'failed' => ['failed', 'failed'],
            'error' => ['error', 'failed'],
            'created' => ['created', 'pending'],
            'unknown' => ['some_unknown', 'pending'],
        ];
    }

    // ────────────────────────────────────────────────────────────
    // NovaPayGateway — confirm
    // ────────────────────────────────────────────────────────────

    public function test_confirm_returns_true_for_paid_status_with_external_id(): void
    {
        $gateway = app(NovaPayGateway::class);
        $this->assertTrue($gateway->confirm(['external_id' => '42', 'status' => 'paid']));
    }

    public function test_confirm_returns_true_when_external_id_in_nested_payments(): void
    {
        $gateway = app(NovaPayGateway::class);
        $this->assertTrue($gateway->confirm([
            'status' => 'paid',
            'payments' => [['external_id' => '42']],
        ]));
    }

    public function test_confirm_returns_false_for_non_paid_status(): void
    {
        $gateway = app(NovaPayGateway::class);
        $this->assertFalse($gateway->confirm(['external_id' => '42', 'status' => 'declined']));
    }

    public function test_confirm_returns_false_when_external_id_missing(): void
    {
        $gateway = app(NovaPayGateway::class);
        $this->assertFalse($gateway->confirm(['status' => 'paid']));
    }

    public function test_confirm_returns_false_when_status_missing(): void
    {
        $gateway = app(NovaPayGateway::class);
        $this->assertFalse($gateway->confirm(['external_id' => '42']));
    }

    public function test_confirm_returns_false_when_external_id_not_numeric(): void
    {
        $gateway = app(NovaPayGateway::class);
        $this->assertFalse($gateway->confirm(['external_id' => 'not-a-number', 'status' => 'paid']));
    }

    // ────────────────────────────────────────────────────────────
    // WebhookProcessor — NovaPay external_id format
    // ────────────────────────────────────────────────────────────

    public function test_webhook_processor_extracts_order_id_from_external_id(): void
    {
        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['session_id' => 'session-xyz'],
        ]);

        $processor = app(WebhookProcessor::class);
        $result = $processor->process('novapay', [
            'external_id' => (string) $order->id,
            'status' => 'paid',
        ]);

        $this->assertTrue($result);
        $this->assertEquals('paid', $order->fresh()->paymentInvoices()->latest()->first()->status);
    }

    public function test_webhook_processor_extracts_order_id_from_nested_payments_external_id(): void
    {
        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['session_id' => 'session-xyz'],
        ]);

        $processor = app(WebhookProcessor::class);
        $result = $processor->process('novapay', [
            'status' => 'paid',
            'payments' => [['external_id' => (string) $order->id]],
        ]);

        $this->assertTrue($result);
    }

    // ────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────

    private function makeClient(): NovaPayClient
    {
        return new NovaPayClient(
            merchantId: self::MERCHANT_ID,
            privateKey: self::PRIVATE_KEY,
            apiUrl: 'https://api-ecom.novapay.ua/v1',
        );
    }

    private function makeOrderWithCredentials(float $cost = 100.0, float $delivery = 50.0): Order
    {
        $payMethod = PayMethod::create([
            'title' => json_encode(['uk' => 'NovaPay']),
            'slug' => 'novapay_'.uniqid(),
            'gateway' => 'novapay',
            'is_active' => true,
            'commission_percent' => 0,
            'priority' => 1,
            'use_hold' => false,
        ]);

        PaymentCredential::create([
            'pay_method_id' => $payMethod->id,
            'is_default' => true,
            'credentials' => [
                'merchant_id' => self::MERCHANT_ID,
                'private_key' => self::PRIVATE_KEY,
            ],
        ]);

        return Order::create([
            'pay_method_id' => $payMethod->id,
            'name' => 'Test User',
            'phone' => '+380000000000',
            'email' => 'test@test.com',
            'address' => 'Test address',
            'cost' => $cost,
            'price_delivery' => $delivery,
            'order_status_id' => 1,
            'first_name' => 'Test',
        ]);
    }
}
