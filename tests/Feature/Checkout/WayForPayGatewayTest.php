<?php

namespace Tests\Feature\Checkout;

use App\Models\Order;
use App\Models\PaymentCredential;
use App\Models\PaymentInvoice;
use App\Models\PayMethod;
use App\Services\Payment\Gateways\WayForPay\WayForPayClient;
use App\Services\Payment\Gateways\WayForPay\WayForPayGateway;
use App\Services\Payment\WebhookProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WayForPayGatewayTest extends TestCase
{
    use RefreshDatabase;

    private const ACCOUNT = 'test_merchant';

    private const SECRET = 'test_secret_key';

    private const DOMAIN = 'test.example.com';

    // ────────────────────────────────────────────────────────────
    // WayForPayClient — buildFormData
    // ────────────────────────────────────────────────────────────

    public function test_client_build_form_data_contains_required_fields(): void
    {
        $client = $this->makeClient();
        $result = $client->buildFormData([
            'orderReference' => '42',
            'orderDate' => 1700000000,
            'amount' => 100.0,
            'currency' => 'UAH',
            'productName' => ['Order #42'],
            'productCount' => [1],
            'productPrice' => [100.0],
            'transactionType' => 'HOLD',
        ]);

        $this->assertEquals(self::ACCOUNT, $result['merchantAccount']);
        $this->assertEquals(self::DOMAIN, $result['merchantDomain']);
        $this->assertEquals('https://secure.wayforpay.com/pay', $result['checkout_url']);
        $this->assertArrayHasKey('merchantSignature', $result);
    }

    public function test_client_form_signature_is_valid_hmac_md5(): void
    {
        $client = $this->makeClient();
        $orderDate = 1700000000;

        $result = $client->buildFormData([
            'orderReference' => '42',
            'orderDate' => $orderDate,
            'amount' => 100.0,
            'currency' => 'UAH',
            'productName' => ['Order #42'],
            'productCount' => [1],
            'productPrice' => [100.0],
            'transactionType' => 'HOLD',
        ]);

        $expected = hash_hmac(
            'md5',
            implode(';', [self::ACCOUNT, self::DOMAIN, '42', $orderDate, '100.00', 'UAH', 'Order #42', 1, '100.00']),
            self::SECRET,
        );

        $this->assertEquals($expected, $result['merchantSignature']);
    }

    // ────────────────────────────────────────────────────────────
    // WayForPayClient — checkStatus
    // ────────────────────────────────────────────────────────────

    public function test_client_check_status_returns_object(): void
    {
        Http::fake([
            'api.wayforpay.com/api' => Http::response(['transactionStatus' => 'Approved'], 200),
        ]);

        $client = $this->makeClient();
        $response = $client->checkStatus('42');

        $this->assertNotNull($response);
        $this->assertEquals('Approved', $response->transactionStatus);
    }

    public function test_client_check_status_returns_null_on_failure(): void
    {
        Http::fake([
            'api.wayforpay.com/api' => Http::response([], 500),
        ]);

        $client = $this->makeClient();

        $this->assertNull($client->checkStatus('42'));
    }

    // ────────────────────────────────────────────────────────────
    // WayForPayClient — capture / void / refund
    // ────────────────────────────────────────────────────────────

    public function test_client_capture_sends_correct_transaction_type(): void
    {
        Http::fake([
            'api.wayforpay.com/api' => Http::response(['transactionStatus' => 'Approved'], 200),
        ]);

        $this->makeClient()->capture('42', 100.0);

        Http::assertSent(fn ($r) => $r->data()['transactionType'] === 'CAPTURE');
    }

    public function test_client_void_sends_correct_transaction_type(): void
    {
        Http::fake([
            'api.wayforpay.com/api' => Http::response(['transactionStatus' => 'Voided'], 200),
        ]);

        $this->makeClient()->void('42');

        Http::assertSent(fn ($r) => $r->data()['transactionType'] === 'VOID');
    }

    public function test_client_refund_sends_correct_transaction_type(): void
    {
        Http::fake([
            'api.wayforpay.com/api' => Http::response(['transactionStatus' => 'Refunded'], 200),
        ]);

        $this->makeClient()->refund('42', 100.0);

        Http::assertSent(fn ($r) => $r->data()['transactionType'] === 'REFUND');
    }

    // ────────────────────────────────────────────────────────────
    // WayForPayClient — verifyWebhookSignature
    // ────────────────────────────────────────────────────────────

    public function test_client_verifies_valid_webhook_signature(): void
    {
        $payload = $this->makeWebhookPayload('42', 'Approved', 100.0);

        $this->assertTrue($this->makeClient()->verifyWebhookSignature($payload));
    }

    public function test_client_rejects_invalid_webhook_signature(): void
    {
        $payload = $this->makeWebhookPayload('42', 'Approved', 100.0);
        $payload['merchantSignature'] = 'invalid_signature';

        $this->assertFalse($this->makeClient()->verifyWebhookSignature($payload));
    }

    // ────────────────────────────────────────────────────────────
    // WayForPayClient — buildWebhookResponse
    // ────────────────────────────────────────────────────────────

    public function test_client_webhook_response_contains_accept_status(): void
    {
        $response = $this->makeClient()->buildWebhookResponse('42');

        $this->assertEquals('42', $response['orderReference']);
        $this->assertEquals('accept', $response['status']);
        $this->assertArrayHasKey('signature', $response);
        $this->assertArrayHasKey('time', $response);
    }

    // ────────────────────────────────────────────────────────────
    // WayForPayGateway — init
    // ────────────────────────────────────────────────────────────

    public function test_gateway_init_uses_hold_when_use_hold_is_true(): void
    {
        $order = $this->makeOrderWithCredentials(useHold: true);
        $gateway = app(WayForPayGateway::class);
        $result = $gateway->init($order);

        $this->assertEquals(self::ACCOUNT, $result['merchantAccount']);
        $this->assertEquals(self::DOMAIN, $result['merchantDomain']);
        $this->assertEquals((string) $order->id, $result['orderReference']);
        $this->assertEquals('HOLD', $result['transactionType']);
        $this->assertEquals('UAH', $result['currency']);
        $this->assertArrayHasKey('merchantSignature', $result);
        $this->assertEquals('https://secure.wayforpay.com/pay', $result['checkout_url']);
    }

    public function test_gateway_init_uses_purchase_when_use_hold_is_false(): void
    {
        $order = $this->makeOrderWithCredentials(useHold: false);
        $result = app(WayForPayGateway::class)->init($order);

        $this->assertEquals('PURCHASE', $result['transactionType']);
    }

    public function test_gateway_init_sets_service_url_and_return_url(): void
    {
        $order = $this->makeOrderWithCredentials();
        $gateway = app(WayForPayGateway::class);
        $result = $gateway->init($order);

        $this->assertStringContainsString('wayforpay', $result['serviceUrl']);
        $this->assertStringContainsString('checkout/success', $result['returnUrl']);
    }

    public function test_gateway_init_sends_real_products_list(): void
    {
        $order    = $this->makeOrderWithCredentials();
        $product1 = \App\Models\Product::factory()->create();
        $product2 = \App\Models\Product::factory()->create();

        \App\Models\OrderProduct::create([
            'order_id'   => $order->id,
            'product_id' => $product1->id,
            'title'      => 'Тестовий товар',
            'count'      => 3,
            'price'      => 100.0,
        ]);
        \App\Models\OrderProduct::create([
            'order_id'   => $order->id,
            'product_id' => $product2->id,
            'title'      => 'Другий товар',
            'count'      => 1,
            'price'      => 200.0,
        ]);

        $result = app(WayForPayGateway::class)->init($order);

        $this->assertCount(2, $result['productName']);
        $this->assertEquals('Тестовий товар', $result['productName'][0]);
        $this->assertEquals(3, $result['productCount'][0]);
        $this->assertEquals(100.0, $result['productPrice'][0]);
        $this->assertEquals('Другий товар', $result['productName'][1]);
    }

    public function test_gateway_init_sends_fallback_product_when_no_products(): void
    {
        $order  = $this->makeOrderWithCredentials(100.0);
        $result = app(WayForPayGateway::class)->init($order);

        $this->assertCount(1, $result['productName']);
        $this->assertCount(1, $result['productCount']);
        $this->assertCount(1, $result['productPrice']);
        $this->assertEquals(1, $result['productCount'][0]);
    }

    // ────────────────────────────────────────────────────────────
    // WayForPayGateway — status
    // ────────────────────────────────────────────────────────────

    public function test_status_returns_pending_when_no_order_reference(): void
    {
        $invoice = new PaymentInvoice(['gateway_response' => []]);
        $gateway = app(WayForPayGateway::class);

        $this->assertEquals('pending', $gateway->status($invoice));
    }

    #[DataProvider('wfpStatusProvider')]
    public function test_status_maps_wfp_response_correctly(string $wfpStatus, string $expected): void
    {
        Http::fake([
            'api.wayforpay.com/api' => Http::response(['transactionStatus' => $wfpStatus], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        $invoice = PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['orderReference' => (string) $order->id],
        ]);

        $gateway = app(WayForPayGateway::class);

        $this->assertEquals($expected, $gateway->status($invoice));
    }

    /** @return array<string, array{string, string}> */
    public static function wfpStatusProvider(): array
    {
        return [
            'Approved' => ['Approved', 'paid'],
            'WaitingAuthComplete' => ['WaitingAuthComplete', 'hold'],
            'InProcessing' => ['InProcessing', 'processing'],
            'Declined' => ['Declined', 'failed'],
            'Expired' => ['Expired', 'failed'],
            'Refunded' => ['Refunded', 'refunded'],
            'Voided' => ['Voided', 'cancelled'],
            'unknown' => ['SomeUnknownStatus', 'pending'],
        ];
    }

    // ────────────────────────────────────────────────────────────
    // WayForPayGateway — confirm
    // ────────────────────────────────────────────────────────────

    public function test_confirm_returns_true_for_approved_with_valid_signature(): void
    {
        $order = $this->makeOrderWithCredentials();
        $payload = $this->makeWebhookPayload((string) $order->id, 'Approved', 100.0);

        $gateway = app(WayForPayGateway::class);

        $this->assertTrue($gateway->confirm($payload));
    }

    public function test_confirm_returns_false_for_declined_status(): void
    {
        $order = $this->makeOrderWithCredentials();
        $payload = $this->makeWebhookPayload((string) $order->id, 'Declined', 100.0);

        $gateway = app(WayForPayGateway::class);

        $this->assertFalse($gateway->confirm($payload));
    }

    public function test_confirm_returns_false_for_invalid_signature(): void
    {
        $order = $this->makeOrderWithCredentials();
        $payload = $this->makeWebhookPayload((string) $order->id, 'Approved', 100.0);
        $payload['merchantSignature'] = 'invalid_sig';

        $gateway = app(WayForPayGateway::class);

        $this->assertFalse($gateway->confirm($payload));
    }

    public function test_confirm_returns_false_when_order_not_found(): void
    {
        $payload = $this->makeWebhookPayload('999999', 'Approved', 100.0);
        $gateway = app(WayForPayGateway::class);

        $this->assertFalse($gateway->confirm($payload));
    }

    public function test_confirm_returns_false_when_no_order_reference(): void
    {
        $gateway = app(WayForPayGateway::class);

        $this->assertFalse($gateway->confirm([]));
    }

    // ────────────────────────────────────────────────────────────
    // WayForPayGateway — captureHold / releaseHold / return
    // ────────────────────────────────────────────────────────────

    public function test_capture_hold_returns_false_when_no_invoice(): void
    {
        $order = $this->makeOrderWithCredentials();
        $gateway = app(WayForPayGateway::class);

        $this->assertFalse($gateway->captureHold($order));
    }

    public function test_capture_hold_calls_capture_endpoint(): void
    {
        Http::fake([
            'api.wayforpay.com/api' => Http::response(['transactionStatus' => 'Approved'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'hold',
            'gateway_response' => ['orderReference' => (string) $order->id],
        ]);

        $gateway = app(WayForPayGateway::class);

        $this->assertTrue($gateway->captureHold($order));
        Http::assertSent(fn ($r) => $r->data()['transactionType'] === 'CAPTURE');
    }

    public function test_release_hold_calls_void_endpoint(): void
    {
        Http::fake([
            'api.wayforpay.com/api' => Http::response(['transactionStatus' => 'Voided'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'hold',
            'gateway_response' => ['orderReference' => (string) $order->id],
        ]);

        $gateway = app(WayForPayGateway::class);

        $this->assertTrue($gateway->releaseHold($order));
        Http::assertSent(fn ($r) => $r->data()['transactionType'] === 'VOID');
    }

    public function test_return_calls_refund_endpoint(): void
    {
        Http::fake([
            'api.wayforpay.com/api' => Http::response(['transactionStatus' => 'Refunded'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'paid',
            'gateway_response' => ['orderReference' => (string) $order->id],
        ]);

        $gateway = app(WayForPayGateway::class);

        $this->assertTrue($gateway->return($order));
        Http::assertSent(fn ($r) => $r->data()['transactionType'] === 'REFUND');
    }

    // ────────────────────────────────────────────────────────────
    // WebhookProcessor — WayForPay orderReference format
    // ────────────────────────────────────────────────────────────

    public function test_webhook_processor_extracts_order_id_from_order_reference(): void
    {
        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['orderReference' => (string) $order->id],
        ]);

        $payload = $this->makeWebhookPayload((string) $order->id, 'Approved', 100.0);

        $processor = app(WebhookProcessor::class);
        $result = $processor->process('wayforpay', $payload);

        $this->assertTrue($result);
        $this->assertEquals('paid', $order->fresh()->paymentInvoices()->latest()->first()->status);
    }

    // ────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────

    private function makeClient(): WayForPayClient
    {
        return new WayForPayClient(self::ACCOUNT, self::SECRET, self::DOMAIN);
    }

    private function makeOrderWithCredentials(float $cost = 100.0, float $delivery = 50.0, bool $useHold = false): Order
    {
        $payMethod = PayMethod::create([
            'title' => json_encode(['uk' => 'WayForPay']),
            'slug' => 'wayforpay_'.uniqid(),
            'gateway' => 'wayforpay',
            'is_active' => true,
            'commission_percent' => 0,
            'priority' => 1,
            'use_hold' => $useHold,
        ]);

        PaymentCredential::create([
            'pay_method_id' => $payMethod->id,
            'is_default' => true,
            'credentials' => [
                'merchant_account' => self::ACCOUNT,
                'merchant_secret' => self::SECRET,
                'merchant_domain' => self::DOMAIN,
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
     * Будує валідний webhook payload із правильним HMAC-MD5 підписом.
     *
     * @return array<string, mixed>
     */
    private function makeWebhookPayload(
        string $orderReference,
        string $transactionStatus,
        float $amount,
        string $authCode = 'auth123',
        string $cardPan = '4242****4242',
        int $reasonCode = 1100,
    ): array {
        $currency = 'UAH';
        $signature = hash_hmac(
            'md5',
            implode(';', [
                self::ACCOUNT, $orderReference,
                number_format($amount, 2, '.', ''),
                $currency, $authCode, $cardPan, $transactionStatus, (string) $reasonCode,
            ]),
            self::SECRET,
        );

        return [
            'merchantAccount' => self::ACCOUNT,
            'orderReference' => $orderReference,
            'transactionStatus' => $transactionStatus,
            'amount' => $amount,
            'currency' => $currency,
            'authCode' => $authCode,
            'cardPan' => $cardPan,
            'reasonCode' => $reasonCode,
            'merchantSignature' => $signature,
        ];
    }
}
