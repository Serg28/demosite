<?php

namespace Tests\Feature\Checkout;

use App\Models\Order;
use App\Models\PayMethod;
use App\Models\PaymentCredential;
use App\Models\PaymentInvoice;
use App\Services\Payment\Gateways\MonoPay\MonoPayClient;
use App\Services\Payment\Gateways\MonoPay\MonoPayGateway;
use App\Services\Payment\Gateways\MonoPay\MonoPayPartsGateway;
use App\Services\Payment\WebhookProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MonoPayGatewayTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'test_mono_token';

    // ────────────────────────────────────────────────────────────
    // MonoPayClient — create
    // ────────────────────────────────────────────────────────────

    public function test_client_create_returns_invoice_data(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/create' => Http::response([
                'invoiceId' => 'mono_abc123',
                'pageUrl'   => 'https://pay.monobank.ua/pay/mono_abc123',
            ], 200),
        ]);

        $client = new MonoPayClient(self::TOKEN);
        $result = $client->create([
            'amount'      => 10000,
            'ccy'         => 980,
            'paymentType' => 'hold',
        ]);

        $this->assertNotNull($result);
        $this->assertEquals('mono_abc123', $result['invoiceId']);
        $this->assertStringContainsString('pay.monobank.ua', $result['pageUrl']);
    }

    public function test_client_create_returns_null_on_api_failure(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/create' => Http::response([], 500),
        ]);

        $client = new MonoPayClient(self::TOKEN);
        $result = $client->create(['amount' => 10000, 'ccy' => 980]);

        $this->assertNull($result);
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayClient — status
    // ────────────────────────────────────────────────────────────

    public function test_client_status_returns_object(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/status*' => Http::response([
                'invoiceId' => 'mono_abc123',
                'status'    => 'success',
            ], 200),
        ]);

        $client   = new MonoPayClient(self::TOKEN);
        $response = $client->status('mono_abc123');

        $this->assertNotNull($response);
        $this->assertEquals('success', $response->status);
    }

    public function test_client_status_returns_null_on_failure(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/status*' => Http::response([], 404),
        ]);

        $client = new MonoPayClient(self::TOKEN);

        $this->assertNull($client->status('mono_invalid'));
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayClient — finalize & cancel
    // ────────────────────────────────────────────────────────────

    public function test_client_finalize_sends_correct_request(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/finalize' => Http::response(['status' => 'ok'], 200),
        ]);

        $client   = new MonoPayClient(self::TOKEN);
        $response = $client->finalize('mono_abc123', 10000);

        $this->assertNotNull($response);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/invoice/finalize')
                && $request->data()['invoiceId'] === 'mono_abc123'
                && $request->data()['amount'] === 10000;
        });
    }

    public function test_client_cancel_sends_correct_request(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/cancel' => Http::response(['status' => 'ok'], 200),
        ]);

        $client   = new MonoPayClient(self::TOKEN);
        $response = $client->cancel('mono_abc123');

        $this->assertNotNull($response);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/invoice/cancel')
                && $request->data()['invoiceId'] === 'mono_abc123';
        });
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayClient — verifyWebhookSignature
    // ────────────────────────────────────────────────────────────

    public function test_client_verifies_valid_rsa_signature(): void
    {
        [$privKey, $pubkeyPem] = $this->generateRsaKeyPair();
        $body  = json_encode(['status' => 'success', 'reference' => '42']);
        $xSign = $this->signBody($body, $privKey);

        Cache::put('monopay_webhook_pubkey', $pubkeyPem, 86400);

        $client = new MonoPayClient(self::TOKEN);

        $this->assertTrue($client->verifyWebhookSignature($body, $xSign));
    }

    public function test_client_rejects_invalid_rsa_signature(): void
    {
        [, $pubkeyPem] = $this->generateRsaKeyPair();
        $body  = json_encode(['status' => 'success', 'reference' => '42']);
        $xSign = base64_encode('invalid_signature_bytes');

        Cache::put('monopay_webhook_pubkey', $pubkeyPem, 86400);

        $client = new MonoPayClient(self::TOKEN);

        $this->assertFalse($client->verifyWebhookSignature($body, $xSign));
    }

    public function test_client_fetches_pubkey_from_api_when_not_cached(): void
    {
        Cache::forget('monopay_webhook_pubkey');
        [, $pubkeyPem] = $this->generateRsaKeyPair();

        Http::fake([
            'api.monobank.ua/api/merchant/pubkey' => Http::response($pubkeyPem, 200),
        ]);

        $body  = json_encode(['status' => 'success']);
        $xSign = base64_encode('any');

        $client = new MonoPayClient(self::TOKEN);
        $client->verifyWebhookSignature($body, $xSign);

        Http::assertSent(fn ($r) => str_contains($r->url(), '/merchant/pubkey'));
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayGateway — init
    // ────────────────────────────────────────────────────────────

    public function test_gateway_init_returns_invoice_data(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/create' => Http::response([
                'invoiceId' => 'mono_hold_1',
                'pageUrl'   => 'https://pay.monobank.ua/pay/mono_hold_1',
            ], 200),
        ]);

        $order   = $this->makeOrderWithCredentials();
        $gateway = app(MonoPayGateway::class);
        $result  = $gateway->init($order);

        $this->assertEquals('mono_hold_1', $result['invoice_id']);
        $this->assertStringContainsString('pay.monobank.ua', $result['page_url']);

        Http::assertSent(function ($request) use ($order) {
            $data = $request->data();

            return str_contains($request->url(), '/invoice/create')
                && $data['paymentType'] === 'hold'
                && $data['ccy'] === 980
                && $data['merchantPaymInfo']['reference'] === (string) $order->id;
        });
    }

    public function test_gateway_init_sends_integer_kopecks_amount(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/create' => Http::response([
                'invoiceId' => 'mono_1',
                'pageUrl'   => 'https://pay.monobank.ua/pay/mono_1',
            ], 200),
        ]);

        $order   = $this->makeOrderWithCredentials();
        $gateway = app(MonoPayGateway::class);
        $gateway->init($order);

        // Перевіряємо, що сума передана як ціле число (копійки, не гривні)
        Http::assertSent(fn ($r) => is_int($r->data()['amount'] ?? null));
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayGateway — status
    // ────────────────────────────────────────────────────────────

    public function test_status_returns_pending_when_no_invoice_id(): void
    {
        $invoice = new PaymentInvoice(['gateway_response' => []]);
        $gateway = app(MonoPayGateway::class);

        $this->assertEquals('pending', $gateway->status($invoice));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('monoStatusProvider')]
    public function test_status_maps_mono_response_correctly(string $monoStatus, string $expected): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/status*' => Http::response(['status' => $monoStatus], 200),
        ]);

        $order   = $this->makeOrderWithCredentials();
        $invoice = PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'initiated',
            'gateway_response' => ['invoice_id' => 'mono_abc'],
        ]);

        $gateway = app(MonoPayGateway::class);

        $this->assertEquals($expected, $gateway->status($invoice));
    }

    /** @return array<string, array{string, string}> */
    public static function monoStatusProvider(): array
    {
        return [
            'success'    => ['success', 'paid'],
            'hold'       => ['hold', 'hold'],
            'processing' => ['processing', 'processing'],
            'failure'    => ['failure', 'failed'],
            'reversed'   => ['reversed', 'failed'],
            'expired'    => ['expired', 'failed'],
            'created'    => ['created', 'pending'],
            'unknown'    => ['unknown_status', 'pending'],
        ];
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayGateway — confirm (webhook)
    // ────────────────────────────────────────────────────────────

    public function test_confirm_returns_true_for_success_status(): void
    {
        $gateway = app(MonoPayGateway::class);

        $this->assertTrue($gateway->confirm(['status' => 'success', 'reference' => '1']));
    }

    public function test_confirm_returns_true_for_hold_status(): void
    {
        $gateway = app(MonoPayGateway::class);

        $this->assertTrue($gateway->confirm(['status' => 'hold', 'reference' => '1']));
    }

    public function test_confirm_returns_false_for_failure_status(): void
    {
        $gateway = app(MonoPayGateway::class);

        $this->assertFalse($gateway->confirm(['status' => 'failure']));
    }

    public function test_confirm_verifies_rsa_signature_when_raw_body_present(): void
    {
        [$privKey, $pubkeyPem] = $this->generateRsaKeyPair();
        Cache::put('monopay_webhook_pubkey', $pubkeyPem, 86400);

        $order   = $this->makeOrderWithCredentials();
        $gateway = app(MonoPayGateway::class);

        $body  = json_encode(['status' => 'success', 'reference' => (string) $order->id]);
        $xSign = $this->signBody($body, $privKey);

        $result = $gateway->confirm([
            'status'    => 'success',
            'reference' => (string) $order->id,
            '_raw_body' => $body,
            '_x_sign'   => $xSign,
        ]);

        $this->assertTrue($result);
    }

    public function test_confirm_rejects_invalid_rsa_signature(): void
    {
        [, $pubkeyPem] = $this->generateRsaKeyPair();
        Cache::put('monopay_webhook_pubkey', $pubkeyPem, 86400);

        $order   = $this->makeOrderWithCredentials();
        $gateway = app(MonoPayGateway::class);

        $body = json_encode(['status' => 'success', 'reference' => (string) $order->id]);

        $result = $gateway->confirm([
            'status'    => 'success',
            'reference' => (string) $order->id,
            '_raw_body' => $body,
            '_x_sign'   => base64_encode('wrong_sig'),
        ]);

        $this->assertFalse($result);
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayGateway — captureHold / releaseHold / return
    // ────────────────────────────────────────────────────────────

    public function test_capture_hold_returns_false_when_no_invoice(): void
    {
        $order   = $this->makeOrderWithCredentials();
        $gateway = app(MonoPayGateway::class);

        $this->assertFalse($gateway->captureHold($order));
    }

    public function test_capture_hold_calls_finalize_endpoint(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/finalize' => Http::response(['status' => 'ok'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'hold',
            'gateway_response' => ['invoice_id' => 'mono_hold_1'],
        ]);

        $gateway = app(MonoPayGateway::class);

        $this->assertTrue($gateway->captureHold($order));
        Http::assertSent(fn ($r) => str_contains($r->url(), '/invoice/finalize'));
    }

    public function test_release_hold_calls_cancel_endpoint(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/cancel' => Http::response(['status' => 'ok'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'hold',
            'gateway_response' => ['invoice_id' => 'mono_hold_1'],
        ]);

        $gateway = app(MonoPayGateway::class);

        $this->assertTrue($gateway->releaseHold($order));
    }

    public function test_return_calls_cancel_endpoint(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/cancel' => Http::response(['status' => 'ok'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'paid',
            'gateway_response' => ['invoice_id' => 'mono_paid_1'],
        ]);

        $gateway = app(MonoPayGateway::class);

        $this->assertTrue($gateway->return($order));
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayPartsGateway — init
    // ────────────────────────────────────────────────────────────

    public function test_parts_gateway_init_uses_deferred_payment_type(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/create' => Http::response([
                'invoiceId' => 'mono_parts_1',
                'pageUrl'   => 'https://pay.monobank.ua/pay/mono_parts_1',
            ], 200),
        ]);

        $order   = $this->makeOrderWithCredentials();
        $gateway = app(MonoPayPartsGateway::class);
        $result  = $gateway->init($order);

        $this->assertEquals('mono_parts_1', $result['invoice_id']);
        Http::assertSent(fn ($r) => $r->data()['paymentType'] === 'deferred');
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayPartsGateway — getInstallments / getCommission
    // ────────────────────────────────────────────────────────────

    public function test_parts_get_installments_returns_all_periods(): void
    {
        $gateway      = app(MonoPayPartsGateway::class);
        $installments = $gateway->getInstallments(1000.0);

        $this->assertCount(7, $installments);

        foreach ($installments as $item) {
            $this->assertArrayHasKey('months', $item);
            $this->assertArrayHasKey('monthly', $item);
            $this->assertArrayHasKey('total', $item);
            $this->assertGreaterThan($item['monthly'], $item['total']);
        }
    }

    public function test_parts_monthly_equals_total_divided_by_months(): void
    {
        $gateway = app(MonoPayPartsGateway::class);
        $items   = $gateway->getInstallments(1000.0);

        foreach ($items as $item) {
            $this->assertEquals(
                round($item['total'] / $item['months'], 2),
                $item['monthly'],
            );
        }
    }

    public function test_parts_get_commission_returns_positive_value(): void
    {
        $gateway    = app(MonoPayPartsGateway::class);
        $commission = $gateway->getCommission(1000.0, 6);

        $this->assertGreaterThan(0, $commission);
    }

    public function test_parts_confirm_returns_true_only_for_success(): void
    {
        $gateway = app(MonoPayPartsGateway::class);

        $this->assertTrue($gateway->confirm(['status' => 'success']));
        $this->assertFalse($gateway->confirm(['status' => 'hold']));
        $this->assertFalse($gateway->confirm(['status' => 'failure']));
    }

    // ────────────────────────────────────────────────────────────
    // WebhookProcessor — MonoPay reference format
    // ────────────────────────────────────────────────────────────

    public function test_webhook_processor_extracts_order_id_from_mono_reference(): void
    {
        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'initiated',
            'gateway_response' => ['invoice_id' => 'mono_hold_1'],
        ]);

        Http::fake([
            'api.monobank.ua/*' => Http::response(['status' => 'ok'], 200),
        ]);

        $processor = app(WebhookProcessor::class);
        $result    = $processor->process('monopay', [
            'status'    => 'success',
            'reference' => (string) $order->id,
            'invoiceId' => 'mono_hold_1',
        ]);

        $this->assertTrue($result);
        $this->assertEquals('paid', $order->fresh()->paymentInvoices()->latest()->first()->status);
    }

    // ────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────

    private function makeOrderWithCredentials(float $cost = 100.0, float $delivery = 50.0): Order
    {
        $payMethod = PayMethod::create([
            'title'              => json_encode(['uk' => 'MonoPay']),
            'slug'               => 'monopay_' . uniqid(),
            'gateway'            => 'monopay',
            'is_active'          => true,
            'commission_percent' => 0,
            'priority'           => 1,
        ]);

        PaymentCredential::create([
            'pay_method_id' => $payMethod->id,
            'is_default'    => true,
            'credentials'   => ['token' => self::TOKEN],
        ]);

        return Order::create([
            'pay_method_id'   => $payMethod->id,
            'name'            => 'Test User',
            'phone'           => '+380000000000',
            'email'           => 'test@test.com',
            'address'         => 'Test address',
            'cost'            => $cost,
            'price_delivery'  => $delivery,
            'order_status_id' => 1,
            'first_name'      => 'Test',
        ]);
    }

    /** @return array{0: \OpenSSLAsymmetricKey, 1: string} */
    private function generateRsaKeyPair(): array
    {
        $privateKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        $details = openssl_pkey_get_details($privateKey);

        return [$privateKey, $details['key']];
    }

    private function signBody(string $body, \OpenSSLAsymmetricKey $privateKey): string
    {
        openssl_sign($body, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }
}
