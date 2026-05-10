<?php

namespace Tests\Feature\Checkout;

use App\Models\Order;
use App\Models\PaymentCredential;
use App\Models\PaymentInvoice;
use App\Models\PayMethod;
use App\Services\Payment\Gateways\Platon\PlatonClient;
use App\Services\Payment\Gateways\Platon\PlatonGateway;
use App\Services\Payment\WebhookProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PlatonGatewayTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'test_platon_key';

    private const PASSWORD = 'test_platon_pass';

    private const API_URL = 'https://secure.platononline.com';

    // ────────────────────────────────────────────────────────────
    // PlatonClient — buildFormData
    // ────────────────────────────────────────────────────────────

    public function test_client_build_form_data_contains_required_fields(): void
    {
        $client = $this->makeClient();
        $result = $client->buildFormData([
            'order' => '42',
            'amount' => 100.0,
            'description' => 'Test Order',
            'url' => 'https://callback.url',
            'email' => 'test@test.com',
            'phone' => '+380000000000',
        ]);

        $this->assertEquals(self::KEY, $result['key']);
        $this->assertEquals('CC', $result['payment']);
        $this->assertEquals('42', $result['order']);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('sign', $result);
        $this->assertEquals('https://callback.url', $result['url']);
        $this->assertEquals(self::API_URL.'/payment/auth', $result['checkout_url']);
    }

    public function test_client_data_does_not_contain_order_field(): void
    {
        $client = $this->makeClient();
        $result = $client->buildFormData([
            'order' => '42',
            'amount' => 100.0,
            'description' => 'Test',
            'url' => 'https://url',
        ]);
        $decoded = json_decode(base64_decode($result['data']), true);

        $this->assertArrayNotHasKey('order', $decoded);
        $this->assertArrayHasKey('amount', $decoded);
        $this->assertArrayHasKey('description', $decoded);
        $this->assertArrayHasKey('currency', $decoded);
    }

    public function test_client_data_encodes_amount_as_two_decimal_string(): void
    {
        $client = $this->makeClient();
        $result = $client->buildFormData([
            'order' => '42',
            'amount' => 100.5,
            'description' => 'Test',
            'url' => 'https://url',
        ]);
        $decoded = json_decode(base64_decode($result['data']), true);

        $this->assertEquals('100.50', $decoded['amount']);
        $this->assertEquals('UAH', $decoded['currency']);
    }

    public function test_client_sign_form_uses_all_strrev(): void
    {
        $client = $this->makeClient();
        $data = $client->generateData(100.0, 'Test');
        $callbackUrl = 'https://callback.url';

        $expected = md5(strtoupper(
            strrev(self::KEY).
            strrev('CC').
            strrev($data).
            strrev($callbackUrl).
            strrev(self::PASSWORD)
        ));

        $this->assertEquals($expected, $client->signForm($data, $callbackUrl));
    }

    // ────────────────────────────────────────────────────────────
    // PlatonClient — verifyCallbackSignature
    // ────────────────────────────────────────────────────────────

    public function test_client_verifies_valid_callback_signature(): void
    {
        $client = $this->makeClient();
        $payload = $this->makeCallbackPayload('42', 'SALE');

        $this->assertTrue($client->verifyCallbackSignature($payload));
    }

    public function test_client_rejects_invalid_callback_signature(): void
    {
        $client = $this->makeClient();
        $payload = $this->makeCallbackPayload('42', 'SALE');
        $payload['sign'] = 'invalid_sign';

        $this->assertFalse($client->verifyCallbackSignature($payload));
    }

    // ────────────────────────────────────────────────────────────
    // PlatonClient — checkStatus
    // ────────────────────────────────────────────────────────────

    public function test_client_check_status_returns_object(): void
    {
        Http::fake([
            self::API_URL.'/payment/status' => Http::response(['status' => 'SALE'], 200),
        ]);

        $result = $this->makeClient()->checkStatus('42');

        $this->assertNotNull($result);
        $this->assertEquals('SALE', $result->status);
    }

    public function test_client_check_status_returns_null_on_failure(): void
    {
        Http::fake([
            self::API_URL.'/payment/status' => Http::response([], 500),
        ]);

        $this->assertNull($this->makeClient()->checkStatus('42'));
    }

    // ────────────────────────────────────────────────────────────
    // PlatonGateway — init
    // ────────────────────────────────────────────────────────────

    public function test_gateway_init_returns_form_data_array(): void
    {
        $order = $this->makeOrderWithCredentials();
        $result = app(PlatonGateway::class)->init($order);

        $this->assertIsArray($result);
        $this->assertEquals(self::KEY, $result['key']);
        $this->assertEquals('CC', $result['payment']);
        $this->assertEquals((string) $order->id, $result['order']);
        $this->assertArrayHasKey('sign', $result);
        $this->assertEquals(self::API_URL.'/payment/auth', $result['checkout_url']);
    }

    public function test_gateway_init_does_not_put_order_id_in_data(): void
    {
        $order = $this->makeOrderWithCredentials();
        $result = app(PlatonGateway::class)->init($order);
        $decoded = json_decode(base64_decode($result['data']), true);

        $this->assertArrayNotHasKey('order', $decoded);
    }

    // ────────────────────────────────────────────────────────────
    // PlatonGateway — status
    // ────────────────────────────────────────────────────────────

    public function test_status_returns_pending_when_no_order_in_response(): void
    {
        $invoice = new PaymentInvoice(['gateway_response' => []]);
        $this->assertEquals('pending', app(PlatonGateway::class)->status($invoice));
    }

    #[DataProvider('platonStatusProvider')]
    public function test_status_maps_platon_status_correctly(string $apiStatus, string $expected): void
    {
        Http::fake([
            self::API_URL.'/payment/status' => Http::response(['status' => $apiStatus], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        $invoice = PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['order' => (string) $order->id],
        ]);

        $this->assertEquals($expected, app(PlatonGateway::class)->status($invoice));
    }

    /** @return array<string, array{string, string}> */
    public static function platonStatusProvider(): array
    {
        return [
            'SALE' => ['SALE', 'paid'],
            'CHARGEBACK' => ['CHARGEBACK', 'refunded'],
            'REFUND' => ['REFUND', 'refunded'],
            'VOID' => ['VOID', 'cancelled'],
            'PENDING' => ['PENDING', 'processing'],
            'PROCESSING' => ['PROCESSING', 'processing'],
            'FAIL' => ['FAIL', 'failed'],
            'DECLINED' => ['DECLINED', 'failed'],
            'ERROR' => ['ERROR', 'failed'],
            'unknown' => ['SOME_STATUS', 'pending'],
        ];
    }

    // ────────────────────────────────────────────────────────────
    // PlatonGateway — confirm
    // ────────────────────────────────────────────────────────────

    public function test_confirm_returns_true_for_sale_with_valid_signature(): void
    {
        $order = $this->makeOrderWithCredentials();
        $payload = $this->makeCallbackPayload((string) $order->id, 'SALE');
        $gateway = app(PlatonGateway::class);

        $this->assertTrue($gateway->confirm($payload));
    }

    public function test_confirm_returns_false_for_non_sale_status(): void
    {
        $order = $this->makeOrderWithCredentials();
        $payload = $this->makeCallbackPayload((string) $order->id, 'FAIL');
        $gateway = app(PlatonGateway::class);

        $this->assertFalse($gateway->confirm($payload));
    }

    public function test_confirm_returns_false_for_invalid_signature(): void
    {
        $order = $this->makeOrderWithCredentials();
        $payload = $this->makeCallbackPayload((string) $order->id, 'SALE');
        $payload['sign'] = 'invalid_sign';

        $this->assertFalse(app(PlatonGateway::class)->confirm($payload));
    }

    public function test_confirm_returns_false_when_order_not_found(): void
    {
        $payload = $this->makeCallbackPayload('999999', 'SALE');
        $this->assertFalse(app(PlatonGateway::class)->confirm($payload));
    }

    public function test_confirm_returns_false_when_order_field_missing(): void
    {
        $this->assertFalse(app(PlatonGateway::class)->confirm(['status' => 'SALE', 'sign' => 'abc']));
    }

    // ────────────────────────────────────────────────────────────
    // WebhookProcessor — Platon order field
    // ────────────────────────────────────────────────────────────

    public function test_webhook_processor_extracts_order_id_from_platon_order_field(): void
    {
        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['order' => (string) $order->id],
        ]);

        $payload = $this->makeCallbackPayload((string) $order->id, 'SALE');
        $processor = app(WebhookProcessor::class);
        $result = $processor->process('platon', $payload);

        $this->assertTrue($result);
        $this->assertEquals('paid', $order->fresh()->paymentInvoices()->latest()->first()->status);
    }

    // ────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────

    private function makeClient(): PlatonClient
    {
        return new PlatonClient(self::KEY, self::PASSWORD, self::API_URL);
    }

    private function makeOrderWithCredentials(float $cost = 100.0, float $delivery = 50.0): Order
    {
        $payMethod = PayMethod::create([
            'title' => json_encode(['uk' => 'Platon']),
            'slug' => 'platon_'.uniqid(),
            'gateway' => 'platon',
            'is_active' => true,
            'commission_percent' => 0,
            'priority' => 1,
            'use_hold' => false,
        ]);

        PaymentCredential::create([
            'pay_method_id' => $payMethod->id,
            'is_default' => true,
            'credentials' => [
                'key' => self::KEY,
                'password' => self::PASSWORD,
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

    /**
     * Будує валідний callback payload від Platon з правильним підписом.
     * sign = MD5(strtoupper(strrev(email) + password + order + strrev(card_6+card_4))).
     *
     * @return array<string, mixed>
     */
    private function makeCallbackPayload(
        string $order,
        string $status,
        string $email = 'test@test.com',
        string $card = '424242424242',
    ): array {
        $sign = md5(strtoupper(
            strrev($email).
            self::PASSWORD.
            $order.
            strrev(substr($card, 0, 6).substr($card, -4))
        ));

        return [
            'order' => $order,
            'status' => $status,
            'email' => $email,
            'card' => $card,
            'sign' => $sign,
        ];
    }
}
