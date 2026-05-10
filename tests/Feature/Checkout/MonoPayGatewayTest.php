<?php

namespace Tests\Feature\Checkout;

use App\Models\Order;
use App\Models\PaymentCredential;
use App\Models\PaymentInvoice;
use App\Models\PayMethod;
use App\Services\Payment\Gateways\MonoPay\MonoPartsClient;
use App\Services\Payment\Gateways\MonoPay\MonoPayClient;
use App\Services\Payment\Gateways\MonoPay\MonoPayGateway;
use App\Services\Payment\Gateways\MonoPay\MonoPayPartsGateway;
use App\Services\Payment\WebhookProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
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
                'pageUrl' => 'https://pay.monobank.ua/pay/mono_abc123',
            ], 200),
        ]);

        $client = new MonoPayClient(self::TOKEN);
        $result = $client->create([
            'amount' => 10000,
            'ccy' => 980,
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
                'status' => 'success',
            ], 200),
        ]);

        $client = new MonoPayClient(self::TOKEN);
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

        $client = new MonoPayClient(self::TOKEN);
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

        $client = new MonoPayClient(self::TOKEN);
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
        $body = json_encode(['status' => 'success', 'reference' => '42']);
        $xSign = $this->signBody($body, $privKey);

        Cache::put('monopay_webhook_pubkey', $pubkeyPem, 86400);

        $client = new MonoPayClient(self::TOKEN);

        $this->assertTrue($client->verifyWebhookSignature($body, $xSign));
    }

    public function test_client_rejects_invalid_rsa_signature(): void
    {
        [, $pubkeyPem] = $this->generateRsaKeyPair();
        $body = json_encode(['status' => 'success', 'reference' => '42']);
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

        $body = json_encode(['status' => 'success']);
        $xSign = base64_encode('any');

        $client = new MonoPayClient(self::TOKEN);
        $client->verifyWebhookSignature($body, $xSign);

        Http::assertSent(fn ($r) => str_contains($r->url(), '/merchant/pubkey'));
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayGateway — init
    // ────────────────────────────────────────────────────────────

    public function test_gateway_init_uses_hold_when_use_hold_is_true(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/create' => Http::response([
                'invoiceId' => 'mono_hold_1',
                'pageUrl' => 'https://pay.monobank.ua/pay/mono_hold_1',
            ], 200),
        ]);

        $order = $this->makeOrderWithCredentials(useHold: true);
        $gateway = app(MonoPayGateway::class);
        $result = $gateway->init($order);

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

    public function test_gateway_init_uses_debit_when_use_hold_is_false(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/create' => Http::response([
                'invoiceId' => 'mono_debit_1',
                'pageUrl' => 'https://pay.monobank.ua/pay/mono_debit_1',
            ], 200),
        ]);

        $order = $this->makeOrderWithCredentials(useHold: false);
        app(MonoPayGateway::class)->init($order);

        Http::assertSent(fn ($r) => $r->data()['paymentType'] === 'debit');
    }

    public function test_gateway_init_sends_integer_kopecks_amount(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/create' => Http::response([
                'invoiceId' => 'mono_1',
                'pageUrl' => 'https://pay.monobank.ua/pay/mono_1',
            ], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        $gateway = app(MonoPayGateway::class);
        $gateway->init($order);

        Http::assertSent(fn ($r) => is_int($r->data()['amount'] ?? null));
    }

    public function test_gateway_init_sends_validity_one_week(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/create' => Http::response([
                'invoiceId' => 'mono_1',
                'pageUrl' => 'https://pay.monobank.ua/pay/mono_1',
            ], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        app(MonoPayGateway::class)->init($order);

        Http::assertSent(fn ($r) => $r->data()['validity'] === 3600 * 24 * 7);
    }

    public function test_gateway_init_includes_basket_order_when_products_exist(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/create' => Http::response([
                'invoiceId' => 'mono_1',
                'pageUrl' => 'https://pay.monobank.ua/pay/mono_1',
            ], 200),
        ]);

        $order   = $this->makeOrderWithCredentials();
        $product = \App\Models\Product::factory()->create();
        \App\Models\OrderProduct::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'title'      => 'Тестовий товар',
            'count'      => 2,
            'price'      => 50.0,
        ]);

        app(MonoPayGateway::class)->init($order);

        Http::assertSent(function ($r) {
            $basket = $r->data()['merchantPaymInfo']['basketOrder'] ?? [];

            return count($basket) === 1
                && $basket[0]['name'] === 'Тестовий товар'
                && $basket[0]['qty'] === 2
                && $basket[0]['sum'] === 5000;
        });
    }

    public function test_gateway_init_sends_empty_basket_order_when_no_products(): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/create' => Http::response([
                'invoiceId' => 'mono_1',
                'pageUrl' => 'https://pay.monobank.ua/pay/mono_1',
            ], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        app(MonoPayGateway::class)->init($order);

        Http::assertSent(fn ($r) => ($r->data()['merchantPaymInfo']['basketOrder'] ?? null) === []);
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

    #[DataProvider('monoStatusProvider')]
    public function test_status_maps_mono_response_correctly(string $monoStatus, string $expected): void
    {
        Http::fake([
            'api.monobank.ua/api/merchant/invoice/status*' => Http::response(['status' => $monoStatus], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        $invoice = PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['invoice_id' => 'mono_abc'],
        ]);

        $gateway = app(MonoPayGateway::class);

        $this->assertEquals($expected, $gateway->status($invoice));
    }

    /** @return array<string, array{string, string}> */
    public static function monoStatusProvider(): array
    {
        return [
            'success' => ['success', 'paid'],
            'hold' => ['hold', 'hold'],
            'processing' => ['processing', 'processing'],
            'failure' => ['failure', 'failed'],
            'reversed' => ['reversed', 'refunded'],
            'expired' => ['expired', 'failed'],
            'created' => ['created', 'pending'],
            'unknown' => ['unknown_status', 'pending'],
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

        $order = $this->makeOrderWithCredentials();
        $gateway = app(MonoPayGateway::class);

        $body = json_encode(['status' => 'success', 'reference' => (string) $order->id]);
        $xSign = $this->signBody($body, $privKey);

        $result = $gateway->confirm([
            'status' => 'success',
            'reference' => (string) $order->id,
            '_raw_body' => $body,
            '_x_sign' => $xSign,
        ]);

        $this->assertTrue($result);
    }

    public function test_confirm_rejects_invalid_rsa_signature(): void
    {
        [, $pubkeyPem] = $this->generateRsaKeyPair();
        Cache::put('monopay_webhook_pubkey', $pubkeyPem, 86400);

        $order = $this->makeOrderWithCredentials();
        $gateway = app(MonoPayGateway::class);

        $body = json_encode(['status' => 'success', 'reference' => (string) $order->id]);

        $result = $gateway->confirm([
            'status' => 'success',
            'reference' => (string) $order->id,
            '_raw_body' => $body,
            '_x_sign' => base64_encode('wrong_sig'),
        ]);

        $this->assertFalse($result);
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayGateway — captureHold / releaseHold / return
    // ────────────────────────────────────────────────────────────

    public function test_capture_hold_returns_false_when_no_invoice(): void
    {
        $order = $this->makeOrderWithCredentials();
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
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'hold',
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
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'hold',
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
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'paid',
            'gateway_response' => ['invoice_id' => 'mono_paid_1'],
        ]);

        $gateway = app(MonoPayGateway::class);

        $this->assertTrue($gateway->return($order));
    }

    // ────────────────────────────────────────────────────────────
    // MonoPartsClient — signature
    // ────────────────────────────────────────────────────────────

    public function test_parts_client_signs_request_with_hmac_sha256(): void
    {
        Http::fake([
            'u2-demo-ext.mono.st4g3.com/*' => Http::response(['order_id' => 'uuid-1', 'state' => 'IN_PROCESS'], 200),
        ]);

        $client = new MonoPartsClient('store1', 'secret1', 'https://u2-demo-ext.mono.st4g3.com');
        $client->create(['store_order_id' => '42_123', 'total_sum' => 100.0]);

        Http::assertSent(function ($request): bool {
            return $request->hasHeader('store-id')
                && $request->hasHeader('signature');
        });
    }

    public function test_parts_client_verify_webhook_signature(): void
    {
        $secret = 'test_secret';
        $body   = '{"order_id":"uuid-1","state":"SUCCESS"}';
        $client = new MonoPartsClient('store1', $secret, 'https://u2-demo-ext.mono.st4g3.com');

        $validSignature = base64_encode(hash_hmac('sha256', $body, $secret, true));

        $this->assertTrue($client->verifyWebhookSignature($body, $validSignature));
        $this->assertFalse($client->verifyWebhookSignature($body, 'bad_signature'));
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayPartsGateway — init
    // ────────────────────────────────────────────────────────────

    public function test_parts_gateway_init_calls_mono_parts_api(): void
    {
        Http::fake([
            'u2-demo-ext.mono.st4g3.com/api/order/create' => Http::response([
                'order_id' => 'parts-uuid-1',
                'state'    => 'IN_PROCESS',
            ], 200),
        ]);

        $order   = $this->makePartsOrderWithCredentials();
        $gateway = app(MonoPayPartsGateway::class);
        $result  = $gateway->init($order);

        $this->assertEquals('parts-uuid-1', $result['mono_order_id']);
        $this->assertStringContainsString((string) $order->id, $result['store_order_id']);
        $this->assertStringContainsString('checkout', $result['url']);
    }

    public function test_parts_gateway_init_sends_payment_installments_type(): void
    {
        Http::fake([
            'u2-demo-ext.mono.st4g3.com/api/order/create' => Http::response(['order_id' => 'uuid-2'], 200),
        ]);

        $order   = $this->makePartsOrderWithCredentials();
        $gateway = app(MonoPayPartsGateway::class);
        $gateway->init($order);

        Http::assertSent(function ($request): bool {
            $data = $request->data();
            return ($data['available_programs'][0]['type'] ?? '') === 'payment_installments';
        });
    }

    public function test_parts_gateway_init_uses_installment_months_as_parts_count(): void
    {
        Http::fake([
            'u2-demo-ext.mono.st4g3.com/api/order/create' => Http::response(['order_id' => 'uuid-3'], 200),
        ]);

        $order = $this->makePartsOrderWithCredentials();
        $order->update(['installment_months' => 12]);

        $gateway = app(MonoPayPartsGateway::class);
        $gateway->init($order);

        Http::assertSent(function ($request): bool {
            $data = $request->data();
            return ($data['available_programs'][0]['available_parts_count'] ?? []) === [12];
        });
    }

    public function test_parts_gateway_init_uses_all_months_when_no_installment_months(): void
    {
        Http::fake([
            'u2-demo-ext.mono.st4g3.com/api/order/create' => Http::response(['order_id' => 'uuid-4'], 200),
        ]);

        $order   = $this->makePartsOrderWithCredentials();
        $gateway = app(MonoPayPartsGateway::class);
        $gateway->init($order);

        Http::assertSent(function ($request): bool {
            $data  = $request->data();
            $parts = $data['available_programs'][0]['available_parts_count'] ?? [];

            return count($parts) === 7;
        });
    }

    public function test_parts_gateway_init_sends_real_products(): void
    {
        Http::fake([
            'u2-demo-ext.mono.st4g3.com/api/order/create' => Http::response(['order_id' => 'uuid-5'], 200),
        ]);

        $order = $this->makePartsOrderWithCredentials();
        $this->attachProductToOrder($order, 'Товар A', 2, 500.0);

        $gateway = app(MonoPayPartsGateway::class);
        $gateway->init($order);

        Http::assertSent(function ($request): bool {
            $data     = $request->data();
            $products = $data['products'] ?? [];

            return count($products) === 1
                && $products[0]['name'] === 'Товар A'
                && $products[0]['count'] === 2
                && $products[0]['sum'] === 500.0;
        });
    }

    public function test_parts_gateway_init_uses_fallback_product_when_no_products(): void
    {
        Http::fake([
            'u2-demo-ext.mono.st4g3.com/api/order/create' => Http::response(['order_id' => 'uuid-6'], 200),
        ]);

        $order   = $this->makePartsOrderWithCredentials();
        $gateway = app(MonoPayPartsGateway::class);
        $gateway->init($order);

        Http::assertSent(function ($request) use ($order): bool {
            $data     = $request->data();
            $products = $data['products'] ?? [];

            return count($products) === 1
                && (float) $products[0]['sum'] === round($order->getTotalCost(), 2);
        });
    }

    public function test_parts_gateway_init_sends_result_callback(): void
    {
        Http::fake([
            'u2-demo-ext.mono.st4g3.com/api/order/create' => Http::response(['order_id' => 'uuid-7'], 200),
        ]);

        $order   = $this->makePartsOrderWithCredentials();
        $gateway = app(MonoPayPartsGateway::class);
        $gateway->init($order);

        Http::assertSent(function ($request): bool {
            $data = $request->data();

            return str_contains($data['result_callback'] ?? '', 'monopayparts');
        });
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayPartsGateway — status
    // ────────────────────────────────────────────────────────────

    /** @dataProvider partsStateProvider */
    public function test_parts_gateway_status_maps_states(string $state, string $subState, string $expected): void
    {
        Http::fake([
            'u2-demo-ext.mono.st4g3.com/api/order/state' => Http::response([
                'order_id'         => 'uuid-state',
                'state'            => $state,
                'order_sub_state'  => $subState,
            ], 200),
        ]);

        $order   = $this->makePartsOrderWithCredentials();
        $invoice = PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'initiated',
            'gateway_response' => ['mono_order_id' => 'uuid-state'],
        ]);

        $gateway = app(MonoPayPartsGateway::class);
        $this->assertEquals($expected, $gateway->status($invoice));
    }

    public static function partsStateProvider(): array
    {
        return [
            'success_active'                => ['SUCCESS', 'ACTIVE', 'paid'],
            'success_done'                  => ['SUCCESS', 'DONE', 'paid'],
            'success_early_closed'          => ['SUCCESS', 'EARLY_CLOSED_BY_CLIENT', 'paid'],
            'success_returned'              => ['SUCCESS', 'RETURNED', 'refunded'],
            'in_process_waiting_for_store'  => ['IN_PROCESS', 'WAITING_FOR_STORE_CONFIRM', 'hold'],
            'in_process_added'              => ['IN_PROCESS', 'ADDED', 'processing'],
            'fail_rejected_by_client'       => ['FAIL', 'REJECTED_BY_CLIENT', 'failed'],
            'fail_not_enough_money'         => ['FAIL', 'NOT_ENOUGH_MONEY_FOR_INIT_DEBIT', 'failed'],
            'empty'                         => ['', '', 'pending'],
        ];
    }

    public function test_parts_gateway_status_returns_pending_when_no_mono_order_id(): void
    {
        $order   = $this->makePartsOrderWithCredentials();
        $invoice = PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'initiated',
            'gateway_response' => [],
        ]);

        $gateway = app(MonoPayPartsGateway::class);
        $this->assertEquals('pending', $gateway->status($invoice));
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayPartsGateway — confirm
    // ────────────────────────────────────────────────────────────

    public function test_parts_confirm_returns_true_for_waiting_for_store_confirm(): void
    {
        $gateway = app(MonoPayPartsGateway::class);

        $this->assertTrue($gateway->confirm([
            'state'           => 'IN_PROCESS',
            'order_sub_state' => 'WAITING_FOR_STORE_CONFIRM',
        ]));
    }

    public function test_parts_confirm_returns_true_for_success_active(): void
    {
        $gateway = app(MonoPayPartsGateway::class);

        $this->assertTrue($gateway->confirm([
            'state'           => 'SUCCESS',
            'order_sub_state' => 'ACTIVE',
        ]));
    }

    public function test_parts_confirm_returns_true_for_success_done(): void
    {
        $gateway = app(MonoPayPartsGateway::class);

        $this->assertTrue($gateway->confirm([
            'state'           => 'SUCCESS',
            'order_sub_state' => 'DONE',
        ]));
    }

    public function test_parts_confirm_returns_false_for_fail(): void
    {
        $gateway = app(MonoPayPartsGateway::class);

        $this->assertFalse($gateway->confirm([
            'state'           => 'FAIL',
            'order_sub_state' => 'REJECTED_BY_CLIENT',
        ]));
    }

    public function test_parts_confirm_returns_false_for_in_process_added(): void
    {
        $gateway = app(MonoPayPartsGateway::class);

        $this->assertFalse($gateway->confirm([
            'state'           => 'IN_PROCESS',
            'order_sub_state' => 'ADDED',
        ]));
    }

    // ────────────────────────────────────────────────────────────
    // MonoPayPartsGateway — captureHold / releaseHold / return
    // ────────────────────────────────────────────────────────────

    public function test_parts_capture_hold_calls_confirm_api(): void
    {
        Http::fake([
            'u2-demo-ext.mono.st4g3.com/api/order/confirm' => Http::response([
                'order_id' => 'uuid-hold',
                'state'    => 'SUCCESS',
            ], 200),
        ]);

        $order   = $this->makePartsOrderWithCredentials();
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'paid',
            'gateway_response' => ['mono_order_id' => 'uuid-hold'],
        ]);

        $gateway = app(MonoPayPartsGateway::class);
        $this->assertTrue($gateway->captureHold($order));
        Http::assertSent(fn ($r) => str_contains($r->url(), '/api/order/confirm'));
    }

    public function test_parts_capture_hold_returns_false_when_no_invoice(): void
    {
        $order   = $this->makePartsOrderWithCredentials();
        $gateway = app(MonoPayPartsGateway::class);

        $this->assertFalse($gateway->captureHold($order));
    }

    public function test_parts_release_hold_calls_reject_api(): void
    {
        Http::fake([
            'u2-demo-ext.mono.st4g3.com/api/order/reject' => Http::response([
                'order_id' => 'uuid-reject',
                'state'    => 'FAIL',
            ], 200),
        ]);

        $order = $this->makePartsOrderWithCredentials();
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'paid',
            'gateway_response' => ['mono_order_id' => 'uuid-reject'],
        ]);

        $gateway = app(MonoPayPartsGateway::class);
        $this->assertTrue($gateway->releaseHold($order));
        Http::assertSent(fn ($r) => str_contains($r->url(), '/api/order/reject'));
    }

    public function test_parts_return_calls_return_api(): void
    {
        Http::fake([
            'u2-demo-ext.mono.st4g3.com/api/order/return' => Http::response([
                'order_id' => 'uuid-return',
                'state'    => 'SUCCESS',
            ], 200),
        ]);

        $order = $this->makePartsOrderWithCredentials();
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'paid',
            'gateway_response' => ['mono_order_id' => 'uuid-return'],
        ]);

        $gateway = app(MonoPayPartsGateway::class);
        $this->assertTrue($gateway->return($order));
        Http::assertSent(function ($request): bool {
            $data = $request->data();

            return str_contains($request->url(), '/api/order/return')
                && isset($data['sum'])
                && ($data['return_money_to_card'] ?? false) === true;
        });
    }

    public function test_parts_return_sends_unique_store_return_id(): void
    {
        Http::fake([
            'u2-demo-ext.mono.st4g3.com/api/order/return' => Http::response(['order_id' => 'uuid-r', 'state' => 'SUCCESS'], 200),
        ]);

        $order = $this->makePartsOrderWithCredentials();
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'paid',
            'gateway_response' => ['mono_order_id' => 'uuid-r'],
        ]);

        $gateway = app(MonoPayPartsGateway::class);
        $gateway->return($order);

        Http::assertSent(function ($request) use ($order): bool {
            $data = $request->data();

            return str_starts_with($data['store_return_id'] ?? '', $order->id . '_ret_');
        });
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

    // ────────────────────────────────────────────────────────────
    // WebhookProcessor — MonoParts UUID order_id lookup
    // ────────────────────────────────────────────────────────────

    public function test_webhook_processor_finds_order_by_mono_parts_uuid(): void
    {
        $order = $this->makePartsOrderWithCredentials();
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'initiated',
            'gateway_response' => ['mono_order_id' => 'some-uuid-abc'],
        ]);

        Http::fake([
            'u2-demo-ext.mono.st4g3.com/*' => Http::response(['state' => 'SUCCESS'], 200),
        ]);

        $processor = app(WebhookProcessor::class);
        $result    = $processor->process('monopayparts', [
            'order_id'         => 'some-uuid-abc',
            'state'            => 'SUCCESS',
            'order_sub_state'  => 'ACTIVE',
        ]);

        $this->assertTrue($result);
        $this->assertEquals('paid', $order->fresh()->paymentInvoices()->latest()->first()->status);
    }

    public function test_webhook_processor_returns_false_for_unknown_mono_parts_uuid(): void
    {
        $this->makePartsOrderWithCredentials();

        $processor = app(WebhookProcessor::class);
        $result    = $processor->process('monopayparts', [
            'order_id'         => 'unknown-uuid-xyz',
            'state'            => 'SUCCESS',
            'order_sub_state'  => 'ACTIVE',
        ]);

        $this->assertFalse($result);
    }

    // ────────────────────────────────────────────────────────────
    // WebhookProcessor — MonoPay reference format
    // ────────────────────────────────────────────────────────────

    public function test_webhook_processor_extracts_order_id_from_mono_reference(): void
    {
        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => 100.0,
            'status' => 'initiated',
            'gateway_response' => ['invoice_id' => 'mono_hold_1'],
        ]);

        Http::fake([
            'api.monobank.ua/*' => Http::response(['status' => 'ok'], 200),
        ]);

        $processor = app(WebhookProcessor::class);
        $result = $processor->process('monopay', [
            'status' => 'success',
            'reference' => (string) $order->id,
            'invoiceId' => 'mono_hold_1',
        ]);

        $this->assertTrue($result);
        $this->assertEquals('paid', $order->fresh()->paymentInvoices()->latest()->first()->status);
    }

    // ────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────

    private function makePartsOrderWithCredentials(float $cost = 100.0, float $delivery = 0.0): Order
    {
        $payMethod = PayMethod::create([
            'title'              => json_encode(['uk' => 'MonoParts']),
            'slug'               => 'monopayparts_' . uniqid(),
            'gateway'            => 'monopayparts',
            'is_active'          => true,
            'commission_percent' => 0,
            'priority'           => 1,
        ]);

        \App\Models\PaymentCredential::create([
            'pay_method_id' => $payMethod->id,
            'is_default'    => true,
            'credentials'   => [
                'store_id' => 'test_store_with_confirm',
                'secret'   => 'secret_98765432--123-123',
                'api_url'  => 'https://u2-demo-ext.mono.st4g3.com',
            ],
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

    private function attachProductToOrder(Order $order, string $title, int $count, float $price): void
    {
        $product = \App\Models\Product::factory()->create();
        \App\Models\OrderProduct::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'title'      => $title,
            'price'      => $price,
            'count'      => $count,
        ]);
    }

    private function makeOrderWithCredentials(float $cost = 100.0, float $delivery = 50.0, bool $useHold = false): Order
    {
        $payMethod = PayMethod::create([
            'title' => json_encode(['uk' => 'MonoPay']),
            'slug' => 'monopay_'.uniqid(),
            'gateway' => 'monopay',
            'is_active' => true,
            'commission_percent' => 0,
            'priority' => 1,
            'use_hold' => $useHold,
        ]);

        PaymentCredential::create([
            'pay_method_id' => $payMethod->id,
            'is_default' => true,
            'credentials' => ['token' => self::TOKEN],
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
