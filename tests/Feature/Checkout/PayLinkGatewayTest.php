<?php

namespace Tests\Feature\Checkout;

use App\Models\Order;
use App\Models\PaymentCredential;
use App\Models\PaymentInvoice;
use App\Models\PayMethod;
use App\Services\Payment\Gateways\PayLink\PayLinkClient;
use App\Services\Payment\Gateways\PayLink\PayLinkGateway;
use App\Services\Payment\WebhookProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayLinkGatewayTest extends TestCase
{
    use RefreshDatabase;

    private const MERCHANT_ID = 'test_merchant';

    private const PRIVATE_KEY = 'test_private_key_secret';

    private const API_URL = 'https://api-inst4.paylink.com.ua';

    private const FRAME_URL = 'https://inst4.paylink.com.ua';

    // ────────────────────────────────────────────────────────────
    // PayLinkClient — generateSign
    // ────────────────────────────────────────────────────────────

    public function test_client_sign_uses_hmac_sha1_with_base64_data(): void
    {
        $client = $this->makeClient();
        $data = ['type' => 'getSession', 'merchant' => self::MERCHANT_ID];
        $sign = $client->generateSign($data);

        $jsonData = (string) json_encode($data, JSON_UNESCAPED_UNICODE);
        $base64Data = base64_encode($jsonData);
        $concat = self::PRIVATE_KEY.$base64Data.self::PRIVATE_KEY;
        $expected = base64_encode(hash_hmac('sha1', $concat, self::PRIVATE_KEY, true));

        $this->assertEquals($expected, $sign);
    }

    // ────────────────────────────────────────────────────────────
    // PayLinkClient — getSession
    // ────────────────────────────────────────────────────────────

    public function test_client_get_session_returns_sid(): void
    {
        Http::fake([
            self::API_URL => Http::response(['sid' => 'session-xyz-123'], 200),
        ]);

        $result = $this->makeClient()->getSession([
            'tranid' => '42',
            'amount' => 150.0,
            'description' => 'Order #42',
            'callback' => 'https://callback.url',
        ]);

        $this->assertNotNull($result);
        $this->assertEquals('session-xyz-123', $result['sid']);
    }

    public function test_client_get_session_returns_null_on_failure(): void
    {
        Http::fake([self::API_URL => Http::response([], 500)]);

        $this->assertNull($this->makeClient()->getSession(['tranid' => '42', 'amount' => 100.0]));
    }

    public function test_client_get_session_sends_type_get_session(): void
    {
        Http::fake([self::API_URL => Http::response(['sid' => 'test'], 200)]);

        $this->makeClient()->getSession(['tranid' => '42', 'amount' => 100.0, 'description' => 'Test', 'callback' => 'url']);

        Http::assertSent(fn ($r) => $r['type'] === 'getSession');
    }

    public function test_client_get_session_sends_merchant_id(): void
    {
        Http::fake([self::API_URL => Http::response(['sid' => 'test'], 200)]);

        $this->makeClient()->getSession(['tranid' => '42', 'amount' => 100.0, 'description' => 'Test', 'callback' => 'url']);

        Http::assertSent(fn ($r) => $r['merchant'] === self::MERCHANT_ID);
    }

    // ────────────────────────────────────────────────────────────
    // PayLinkClient — getTranState
    // ────────────────────────────────────────────────────────────

    public function test_client_get_tran_state_returns_result(): void
    {
        Http::fake([
            self::API_URL => Http::response([
                'resultCode' => '100',
                'oper_data' => [['TRANID' => '42', 'AMOUNT' => '150.00']],
            ], 200),
        ]);

        $result = $this->makeClient()->getTranState('session-xyz-123');

        $this->assertNotNull($result);
        $this->assertEquals('100', $result['resultCode']);
        $this->assertEquals('42', $result['oper_data'][0]['TRANID']);
    }

    public function test_client_get_tran_state_sends_type_get_tran_state(): void
    {
        Http::fake([self::API_URL => Http::response(['resultCode' => '100'], 200)]);

        $this->makeClient()->getTranState('test-sid');

        Http::assertSent(fn ($r) => $r['type'] === 'getTranState');
    }

    // ────────────────────────────────────────────────────────────
    // PayLinkClient — getPaymentFrameUrl
    // ────────────────────────────────────────────────────────────

    public function test_client_get_payment_frame_url_contains_sid(): void
    {
        $url = $this->makeClient()->getPaymentFrameUrl('test-sid-123');
        $this->assertEquals(self::FRAME_URL.'/frame/pay/?sid=test-sid-123', $url);
    }

    // ────────────────────────────────────────────────────────────
    // PayLinkGateway — init
    // ────────────────────────────────────────────────────────────

    public function test_gateway_init_returns_frame_url(): void
    {
        Http::fake([
            self::API_URL => Http::response(['sid' => 'session-42'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        $result = app(PayLinkGateway::class)->init($order);

        $this->assertStringContainsString('session-42', $result);
        $this->assertStringContainsString('/frame/pay/?sid=', $result);
    }

    public function test_gateway_init_returns_empty_string_when_session_fails(): void
    {
        Http::fake([self::API_URL => Http::response([], 500)]);

        $order = $this->makeOrderWithCredentials();
        $result = app(PayLinkGateway::class)->init($order);

        $this->assertEquals('', $result);
    }

    public function test_gateway_init_passes_order_id_as_tranid(): void
    {
        Http::fake([self::API_URL => Http::response(['sid' => 'test'], 200)]);

        $order = $this->makeOrderWithCredentials();
        app(PayLinkGateway::class)->init($order);

        Http::assertSent(fn ($r) => $r['tranid'] === (string) $order->id);
    }

    // ────────────────────────────────────────────────────────────
    // PayLinkGateway — status
    // ────────────────────────────────────────────────────────────

    public function test_status_returns_pending_when_no_result_code(): void
    {
        $invoice = new PaymentInvoice(['gateway_response' => []]);
        $this->assertEquals('pending', app(PayLinkGateway::class)->status($invoice));
    }

    public function test_status_returns_paid_for_result_code_100(): void
    {
        $order = $this->makeOrderWithCredentials();
        $invoice = PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['resultCode' => '100', 'sid' => 'session-xyz'],
        ]);

        $this->assertEquals('paid', app(PayLinkGateway::class)->status($invoice));
    }

    public function test_status_returns_failed_for_non_100_result_code(): void
    {
        $order = $this->makeOrderWithCredentials();
        $invoice = PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['resultCode' => '200'],
        ]);

        $this->assertEquals('failed', app(PayLinkGateway::class)->status($invoice));
    }

    // ────────────────────────────────────────────────────────────
    // PayLinkGateway — confirm
    // ────────────────────────────────────────────────────────────

    public function test_confirm_returns_true_for_result_code_100(): void
    {
        $gateway = app(PayLinkGateway::class);
        $this->assertTrue($gateway->confirm(['order_id' => '42', 'resultCode' => '100']));
    }

    public function test_confirm_returns_false_for_non_100_result_code(): void
    {
        $gateway = app(PayLinkGateway::class);
        $this->assertFalse($gateway->confirm(['order_id' => '42', 'resultCode' => '200']));
    }

    public function test_confirm_returns_false_when_order_id_missing(): void
    {
        $gateway = app(PayLinkGateway::class);
        $this->assertFalse($gateway->confirm(['resultCode' => '100']));
    }

    public function test_confirm_returns_false_when_result_code_missing(): void
    {
        $gateway = app(PayLinkGateway::class);
        $this->assertFalse($gateway->confirm(['order_id' => '42']));
    }

    public function test_confirm_returns_false_when_order_id_not_numeric(): void
    {
        $gateway = app(PayLinkGateway::class);
        $this->assertFalse($gateway->confirm(['order_id' => 'not-a-number', 'resultCode' => '100']));
    }

    // ────────────────────────────────────────────────────────────
    // WebhookProcessor — PayLink enriched payload
    // ────────────────────────────────────────────────────────────

    public function test_webhook_processor_handles_paylink_enriched_payload(): void
    {
        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['sid' => 'session-xyz'],
        ]);

        $processor = app(WebhookProcessor::class);
        $result = $processor->process('paylink', [
            'order_id' => (string) $order->id,
            'resultCode' => '100',
        ]);

        $this->assertTrue($result);
        $this->assertEquals('paid', $order->fresh()->paymentInvoices()->latest()->first()->status);
    }

    // ────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────

    private function makeClient(): PayLinkClient
    {
        return new PayLinkClient(
            merchantId: self::MERCHANT_ID,
            privateKey: self::PRIVATE_KEY,
            apiUrl: self::API_URL,
            frameUrl: self::FRAME_URL,
        );
    }

    private function makeOrderWithCredentials(float $cost = 100.0, float $delivery = 50.0): Order
    {
        $payMethod = PayMethod::create([
            'title' => json_encode(['uk' => 'PayLink']),
            'slug' => 'paylink_'.uniqid(),
            'gateway' => 'paylink',
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
