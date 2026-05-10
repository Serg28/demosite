<?php

namespace Tests\Feature\Checkout;

use App\Models\Order;
use App\Models\PaymentCredential;
use App\Models\PaymentInvoice;
use App\Models\PayMethod;
use App\Services\Payment\Gateways\EasyPay\EasyPayClient;
use App\Services\Payment\Gateways\EasyPay\EasyPayGateway;
use App\Services\Payment\WebhookProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EasyPayGatewayTest extends TestCase
{
    use RefreshDatabase;

    private const PARTNER_KEY = 'test_partner_key';

    private const SERVICE_KEY = 'test_service_key';

    private const SECRET_KEY = 'test_secret_key';

    // ────────────────────────────────────────────────────────────
    // EasyPayClient — sign
    // ────────────────────────────────────────────────────────────

    public function test_client_sign_produces_correct_base64_sha256(): void
    {
        $client = $this->makeClient();
        $body = '{"test":"data"}';
        $sign = $client->sign($body);

        $expected = base64_encode(hash('sha256', self::SECRET_KEY.$body, true));

        $this->assertEquals($expected, $sign);
    }

    // ────────────────────────────────────────────────────────────
    // EasyPayClient — createApp
    // ────────────────────────────────────────────────────────────

    public function test_client_create_app_returns_object_on_success(): void
    {
        Http::fake([
            'merchantapi.easypay.ua/api/system/createApp' => Http::response([
                'appId' => 'app-123',
                'pageId' => 'page-456',
            ], 200),
        ]);

        $result = $this->makeClient()->createApp();

        $this->assertNotNull($result);
        $this->assertEquals('app-123', $result->appId);
        $this->assertEquals('page-456', $result->pageId);
    }

    public function test_client_create_app_returns_null_on_failure(): void
    {
        Http::fake([
            'merchantapi.easypay.ua/api/system/createApp' => Http::response([], 500),
        ]);

        $this->assertNull($this->makeClient()->createApp());
    }

    // ────────────────────────────────────────────────────────────
    // EasyPayClient — createOrder
    // ────────────────────────────────────────────────────────────

    public function test_client_create_order_returns_forward_url(): void
    {
        Http::fake([
            'merchantapi.easypay.ua/api/merchant/createOrder' => Http::response([
                'forwardUrl' => 'https://pay.easypay.ua/session/xyz',
            ], 200),
        ]);

        $result = $this->makeClient()->createOrder('app-123', 'page-456', '{}');

        $this->assertNotNull($result);
        $this->assertEquals('https://pay.easypay.ua/session/xyz', $result->forwardUrl);
    }

    public function test_client_create_order_returns_null_on_failure(): void
    {
        Http::fake([
            'merchantapi.easypay.ua/api/merchant/createOrder' => Http::response([], 500),
        ]);

        $this->assertNull($this->makeClient()->createOrder('app-123', 'page-456', '{}'));
    }

    public function test_client_create_order_sends_sign_header(): void
    {
        Http::fake([
            'merchantapi.easypay.ua/api/merchant/createOrder' => Http::response(['forwardUrl' => 'https://url'], 200),
        ]);

        $body = '{"amount":100}';
        $this->makeClient()->createOrder('app-1', 'page-1', $body);

        Http::assertSent(fn ($r) => $r->hasHeader('Sign'));
    }

    // ────────────────────────────────────────────────────────────
    // EasyPayClient — getOrder
    // ────────────────────────────────────────────────────────────

    public function test_client_get_order_returns_status(): void
    {
        Http::fake([
            'merchantapi.easypay.ua/api/merchant/getOrder' => Http::response([
                'orderId' => '42',
                'status' => 'SUCCESS',
            ], 200),
        ]);

        $result = $this->makeClient()->getOrder('42');

        $this->assertNotNull($result);
        $this->assertEquals('SUCCESS', $result->status);
    }

    public function test_client_get_order_returns_null_on_failure(): void
    {
        Http::fake([
            'merchantapi.easypay.ua/api/merchant/getOrder' => Http::response([], 500),
        ]);

        $this->assertNull($this->makeClient()->getOrder('42'));
    }

    // ────────────────────────────────────────────────────────────
    // EasyPayGateway — init
    // ────────────────────────────────────────────────────────────

    public function test_gateway_init_returns_forward_url(): void
    {
        Http::fake([
            'merchantapi.easypay.ua/api/system/createApp' => Http::response(['appId' => 'a1', 'pageId' => 'p1'], 200),
            'merchantapi.easypay.ua/api/merchant/createOrder' => Http::response(['forwardUrl' => 'https://pay.easypay.ua/session/abc'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        $result = app(EasyPayGateway::class)->init($order);

        $this->assertEquals('https://pay.easypay.ua/session/abc', $result);
    }

    public function test_gateway_init_returns_empty_string_when_create_app_fails(): void
    {
        Http::fake([
            'merchantapi.easypay.ua/api/system/createApp' => Http::response([], 500),
        ]);

        $order = $this->makeOrderWithCredentials();
        $result = app(EasyPayGateway::class)->init($order);

        $this->assertEquals('', $result);
    }

    public function test_gateway_init_returns_empty_string_when_create_order_fails(): void
    {
        Http::fake([
            'merchantapi.easypay.ua/api/system/createApp' => Http::response(['appId' => 'a1', 'pageId' => 'p1'], 200),
            'merchantapi.easypay.ua/api/merchant/createOrder' => Http::response([], 500),
        ]);

        $order = $this->makeOrderWithCredentials();
        $result = app(EasyPayGateway::class)->init($order);

        $this->assertEquals('', $result);
    }

    // ────────────────────────────────────────────────────────────
    // EasyPayGateway — status
    // ────────────────────────────────────────────────────────────

    public function test_status_returns_pending_when_no_order_id(): void
    {
        $invoice = new PaymentInvoice(['gateway_response' => []]);
        $this->assertEquals('pending', app(EasyPayGateway::class)->status($invoice));
    }

    #[DataProvider('easyPayStatusProvider')]
    public function test_status_maps_easypay_status_correctly(string $apiStatus, string $expected): void
    {
        Http::fake([
            'merchantapi.easypay.ua/api/merchant/getOrder' => Http::response(['status' => $apiStatus], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        $invoice = PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['orderId' => (string) $order->id],
        ]);

        $this->assertEquals($expected, app(EasyPayGateway::class)->status($invoice));
    }

    /** @return array<string, array{string, string}> */
    public static function easyPayStatusProvider(): array
    {
        return [
            'SUCCESS' => ['SUCCESS', 'paid'],
            'PAID' => ['PAID', 'paid'],
            'PROCESSING' => ['PROCESSING', 'processing'],
            'FAIL' => ['FAIL', 'failed'],
            'ERROR' => ['ERROR', 'failed'],
            'REFUND' => ['REFUND', 'refunded'],
            'unknown' => ['UNKNOWN_STATUS', 'pending'],
        ];
    }

    // ────────────────────────────────────────────────────────────
    // EasyPayGateway — confirm
    // ────────────────────────────────────────────────────────────

    public function test_confirm_returns_true_when_order_id_present_and_no_error(): void
    {
        $gateway = app(EasyPayGateway::class);
        $this->assertTrue($gateway->confirm(['orderId' => '42']));
    }

    public function test_confirm_returns_false_when_order_id_missing(): void
    {
        $gateway = app(EasyPayGateway::class);
        $this->assertFalse($gateway->confirm([]));
    }

    public function test_confirm_returns_false_when_error_present(): void
    {
        $gateway = app(EasyPayGateway::class);
        $this->assertFalse($gateway->confirm(['orderId' => '42', 'error' => 'Payment failed']));
    }

    public function test_confirm_returns_true_when_error_is_null(): void
    {
        $gateway = app(EasyPayGateway::class);
        $this->assertTrue($gateway->confirm(['orderId' => '42', 'error' => null]));
    }

    // ────────────────────────────────────────────────────────────
    // WebhookProcessor — EasyPay orderId format
    // ────────────────────────────────────────────────────────────

    public function test_webhook_processor_handles_easypay_order_id(): void
    {
        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['orderId' => (string) $order->id],
        ]);

        $processor = app(WebhookProcessor::class);
        $result = $processor->process('easypay', ['orderId' => (string) $order->id]);

        $this->assertTrue($result);
        $this->assertEquals('paid', $order->fresh()->paymentInvoices()->latest()->first()->status);
    }

    // ────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────

    private function makeClient(): EasyPayClient
    {
        return new EasyPayClient(self::PARTNER_KEY, self::SERVICE_KEY, self::SECRET_KEY);
    }

    private function makeOrderWithCredentials(float $cost = 100.0, float $delivery = 50.0): Order
    {
        $payMethod = PayMethod::create([
            'title' => json_encode(['uk' => 'EasyPay']),
            'slug' => 'easypay_'.uniqid(),
            'gateway' => 'easypay',
            'is_active' => true,
            'commission_percent' => 0,
            'priority' => 1,
            'use_hold' => false,
        ]);

        PaymentCredential::create([
            'pay_method_id' => $payMethod->id,
            'is_default' => true,
            'credentials' => [
                'partner_key' => self::PARTNER_KEY,
                'service_key' => self::SERVICE_KEY,
                'secret_key' => self::SECRET_KEY,
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
