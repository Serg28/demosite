<?php

namespace Tests\Feature\Checkout;

use App\Models\Order;
use App\Models\PaymentCredential;
use App\Models\PaymentInvoice;
use App\Models\PayMethod;
use App\Services\Payment\Gateways\LiqPay\LiqPayClient;
use App\Services\Payment\Gateways\LiqPay\LiqPayCodGateway;
use App\Services\Payment\Gateways\LiqPay\LiqPayGateway;
use App\Services\Payment\WebhookProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LiqPayGatewayTest extends TestCase
{
    use RefreshDatabase;

    private const PUBLIC_KEY = 'test_public_key';

    private const PRIVATE_KEY = 'test_private_key';

    // ────────────────────────────────────────────────────────────
    // LiqPayClient
    // ────────────────────────────────────────────────────────────

    public function test_client_builds_valid_form_data(): void
    {
        $client = new LiqPayClient(self::PUBLIC_KEY, self::PRIVATE_KEY);

        $result = $client->buildFormData([
            'action' => 'pay',
            'amount' => 100,
            'currency' => 'UAH',
        ]);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('signature', $result);
        $this->assertArrayHasKey('checkout_url', $result);
        $this->assertStringContainsString('liqpay.ua', $result['checkout_url']);

        $decoded = json_decode(base64_decode($result['data']), true);
        $this->assertEquals('3', $decoded['version']);
        $this->assertEquals(self::PUBLIC_KEY, $decoded['public_key']);
        $this->assertEquals('pay', $decoded['action']);
    }

    public function test_client_verifies_valid_signature(): void
    {
        $client = new LiqPayClient(self::PUBLIC_KEY, self::PRIVATE_KEY);
        $data = base64_encode((string) json_encode(['action' => 'pay']));

        $signature = base64_encode(sha1(self::PRIVATE_KEY.$data.self::PRIVATE_KEY, true));

        $this->assertTrue($client->verifySignature($data, $signature));
    }

    public function test_client_rejects_invalid_signature(): void
    {
        $client = new LiqPayClient(self::PUBLIC_KEY, self::PRIVATE_KEY);
        $data = base64_encode((string) json_encode(['action' => 'pay']));

        $this->assertFalse($client->verifySignature($data, 'wrong_signature'));
    }

    public function test_client_decodes_data_field(): void
    {
        $client = new LiqPayClient(self::PUBLIC_KEY, self::PRIVATE_KEY);
        $payload = ['action' => 'pay', 'status' => 'success'];
        $data = base64_encode((string) json_encode($payload));

        $this->assertEquals($payload, $client->decodeData($data));
    }

    public function test_client_calls_api_and_returns_object(): void
    {
        Http::fake([
            'www.liqpay.ua/api/request' => Http::response(['status' => 'success'], 200),
        ]);

        $client = new LiqPayClient(self::PUBLIC_KEY, self::PRIVATE_KEY);
        $response = $client->api('status', ['order_id' => '42_abc']);

        $this->assertEquals('success', $response->status);
    }

    public function test_client_returns_null_on_api_failure(): void
    {
        Http::fake([
            'www.liqpay.ua/api/request' => Http::response([], 500),
        ]);

        $client = new LiqPayClient(self::PUBLIC_KEY, self::PRIVATE_KEY);
        $response = $client->api('status', ['order_id' => '42_abc']);

        $this->assertNull($response);
    }

    // ────────────────────────────────────────────────────────────
    // LiqPayGateway — init
    // ────────────────────────────────────────────────────────────

    public function test_liqpay_gateway_init_uses_hold_when_use_hold_is_true(): void
    {
        $order = $this->makeOrderWithCredentials(useHold: true);
        $gateway = app(LiqPayGateway::class);
        $result = $gateway->init($order);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('liqpay_order_id', $result);
        $this->assertStringStartsWith((string) $order->id.'_', $result['liqpay_order_id']);

        $decoded = json_decode(base64_decode($result['data']), true);
        $this->assertEquals('hold', $decoded['action']);
        $this->assertEquals('UAH', $decoded['currency']);
    }

    public function test_liqpay_gateway_init_uses_pay_when_use_hold_is_false(): void
    {
        $order = $this->makeOrderWithCredentials(useHold: false);
        $gateway = app(LiqPayGateway::class);
        $result = $gateway->init($order);

        $decoded = json_decode(base64_decode($result['data']), true);
        $this->assertEquals('pay', $decoded['action']);
    }

    // ────────────────────────────────────────────────────────────
    // LiqPayGateway — status
    // ────────────────────────────────────────────────────────────

    public function test_status_returns_pending_when_no_liqpay_order_id(): void
    {
        $invoice = new PaymentInvoice(['gateway_response' => []]);
        $gateway = app(LiqPayGateway::class);

        $this->assertEquals('pending', $gateway->status($invoice));
    }

    #[DataProvider('liqpayStatusProvider')]
    public function test_status_maps_liqpay_response_correctly(string $liqpayStatus, string $expected): void
    {
        Http::fake([
            'www.liqpay.ua/api/request' => Http::response(['status' => $liqpayStatus], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        $invoice = PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['liqpay_order_id' => '42_abc'],
        ]);

        $gateway = app(LiqPayGateway::class);

        $this->assertEquals($expected, $gateway->status($invoice));
    }

    /** @return array<string, array{string, string}> */
    public static function liqpayStatusProvider(): array
    {
        return [
            'success' => ['success', 'paid'],
            'hold_wait' => ['hold_wait', 'hold'],
            'processing' => ['processing', 'processing'],
            'failure' => ['failure', 'failed'],
            'error' => ['error', 'failed'],
            'unknown' => ['sandbox', 'pending'],
        ];
    }

    // ────────────────────────────────────────────────────────────
    // LiqPayGateway — confirm (webhook)
    // ────────────────────────────────────────────────────────────

    public function test_confirm_returns_false_when_payload_empty(): void
    {
        $gateway = app(LiqPayGateway::class);

        $this->assertFalse($gateway->confirm([]));
        $this->assertFalse($gateway->confirm(['data' => '']));
    }

    public function test_confirm_returns_false_on_invalid_signature(): void
    {
        $order = $this->makeOrderWithCredentials();
        $gateway = app(LiqPayGateway::class);

        $payload = $this->buildWebhookPayload($order->id, 'success', 'wrong_private_key');
        $this->assertFalse($gateway->confirm($payload));
    }

    public function test_confirm_returns_true_for_success_status(): void
    {
        $order = $this->makeOrderWithCredentials();
        $gateway = app(LiqPayGateway::class);

        $payload = $this->buildWebhookPayload($order->id, 'success');
        $this->assertTrue($gateway->confirm($payload));
    }

    public function test_confirm_returns_true_for_hold_wait_status(): void
    {
        $order = $this->makeOrderWithCredentials();
        $gateway = app(LiqPayGateway::class);

        $payload = $this->buildWebhookPayload($order->id, 'hold_wait');
        $this->assertTrue($gateway->confirm($payload));
    }

    public function test_confirm_returns_false_for_failure_status(): void
    {
        $order = $this->makeOrderWithCredentials();
        $gateway = app(LiqPayGateway::class);

        $payload = $this->buildWebhookPayload($order->id, 'failure');
        $this->assertFalse($gateway->confirm($payload));
    }

    public function test_confirm_returns_false_when_order_not_found(): void
    {
        $gateway = app(LiqPayGateway::class);

        $payload = $this->buildWebhookPayload(99999, 'success');
        $this->assertFalse($gateway->confirm($payload));
    }

    // ────────────────────────────────────────────────────────────
    // LiqPayGateway — captureHold / releaseHold / return
    // ────────────────────────────────────────────────────────────

    public function test_capture_hold_returns_false_when_no_invoice(): void
    {
        $order = $this->makeOrderWithCredentials();
        $gateway = app(LiqPayGateway::class);

        $this->assertFalse($gateway->captureHold($order));
    }

    public function test_capture_hold_calls_liqpay_api(): void
    {
        Http::fake([
            'www.liqpay.ua/api/request' => Http::response(['status' => 'success'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['liqpay_order_id' => '42_abc'],
        ]);

        $gateway = app(LiqPayGateway::class);

        $this->assertTrue($gateway->captureHold($order));
    }

    public function test_release_hold_calls_cancel_action(): void
    {
        Http::fake([
            'www.liqpay.ua/api/request' => Http::response(['status' => 'success'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['liqpay_order_id' => '42_abc'],
        ]);

        $gateway = app(LiqPayGateway::class);

        $this->assertTrue($gateway->releaseHold($order));
    }

    public function test_return_calls_refund_action(): void
    {
        Http::fake([
            'www.liqpay.ua/api/request' => Http::response(['status' => 'success'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'paid',
            'gateway_response' => ['liqpay_order_id' => '42_abc'],
        ]);

        $gateway = app(LiqPayGateway::class);

        $this->assertTrue($gateway->return($order));
    }

    // ────────────────────────────────────────────────────────────
    // LiqPayCodGateway
    // ────────────────────────────────────────────────────────────

    public function test_cod_gateway_init_uses_pay_action(): void
    {
        $order = $this->makeOrderWithCredentials();
        $gateway = app(LiqPayCodGateway::class);

        $result = $gateway->init($order);
        $decoded = json_decode(base64_decode($result['data']), true);

        $this->assertEquals('pay', $decoded['action']);
        $this->assertArrayHasKey('liqpay_order_id', $result);
    }

    public function test_cod_gateway_confirm_accepts_only_success(): void
    {
        $order = $this->makeOrderWithCredentials();
        $gateway = app(LiqPayCodGateway::class);

        $this->assertTrue($gateway->confirm($this->buildWebhookPayload($order->id, 'success')));
        $this->assertFalse($gateway->confirm($this->buildWebhookPayload($order->id, 'hold_wait')));
        $this->assertFalse($gateway->confirm($this->buildWebhookPayload($order->id, 'failure')));
    }

    // ────────────────────────────────────────────────────────────
    // WebhookProcessor — LiqPay payload format
    // ────────────────────────────────────────────────────────────

    public function test_webhook_processor_extracts_order_id_from_liqpay_data(): void
    {
        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['liqpay_order_id' => $order->id.'_abc'],
        ]);

        Http::fake(['www.liqpay.ua/*' => Http::response([], 200)]);

        $payload = $this->buildWebhookPayload($order->id, 'success');
        $processor = app(WebhookProcessor::class);

        $result = $processor->process('liqpay', $payload);

        $this->assertTrue($result);
        $this->assertEquals('paid', $order->fresh()->paymentInvoices()->latest()->first()->status);
    }

    // ────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────

    private function makeOrderWithCredentials(bool $useHold = false): Order
    {
        $payMethod = PayMethod::create([
            'title' => json_encode(['uk' => 'LiqPay']),
            'slug' => 'liqpay_'.uniqid(),
            'is_active' => true,
            'commission_percent' => 2.5,
            'priority' => 1,
            'use_hold' => $useHold,
        ]);

        PaymentCredential::create([
            'pay_method_id' => $payMethod->id,
            'is_default' => true,
            'credentials' => [
                'public_key' => self::PUBLIC_KEY,
                'private_key' => self::PRIVATE_KEY,
            ],
        ]);

        return Order::create([
            'pay_method_id' => $payMethod->id,
            'name' => 'Test User',
            'phone' => '+380000000000',
            'email' => 'test@test.com',
            'address' => 'Test address',
            'cost' => 100.0,
            'price_delivery' => 50.0,
            'order_status_id' => 1,
            'first_name' => 'Test',
        ]);
    }

    /**
     * @return array{data: string, signature: string}
     */
    private function buildWebhookPayload(int $orderId, string $status, string $privateKey = self::PRIVATE_KEY): array
    {
        $params = [
            'order_id' => $orderId.'_abc123',
            'status' => $status,
            'amount' => 100,
            'currency' => 'UAH',
            'public_key' => self::PUBLIC_KEY,
        ];

        $data = base64_encode((string) json_encode($params));
        $signature = base64_encode(sha1($privateKey.$data.$privateKey, true));

        return compact('data', 'signature');
    }
}
