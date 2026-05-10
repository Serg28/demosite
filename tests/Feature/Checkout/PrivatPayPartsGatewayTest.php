<?php

namespace Tests\Feature\Checkout;

use App\Models\Order;
use App\Models\PaymentCredential;
use App\Models\PaymentInvoice;
use App\Models\PayMethod;
use App\Services\Payment\Gateways\Privat\PrivatPayPartsClient;
use App\Services\Payment\Gateways\Privat\PrivatPayPartsGateway;
use App\Services\Payment\WebhookProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PrivatPayPartsGatewayTest extends TestCase
{
    use RefreshDatabase;

    private const STORE_ID = 'test_store';

    private const PASSWORD = 'test_password';

    private const BASE_URL = 'https://payparts2.privatbank.ua/ipp/v2';

    // ────────────────────────────────────────────────────────────
    // PrivatPayPartsClient — createPayment
    // ────────────────────────────────────────────────────────────

    public function test_client_create_payment_returns_token_and_url(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/create' => Http::response([
                'token'      => 'priv_token_123',
                'paymentUrl' => self::BASE_URL . '/payment?token=priv_token_123',
            ], 200),
        ]);

        $result = $this->makeClient()->createPayment(
            '1', 100.0, 'https://site.com/success', 'https://site.com/webhook'
        );

        $this->assertNotNull($result);
        $this->assertEquals('priv_token_123', $result['token']);
        $this->assertStringContainsString('payparts2.privatbank.ua', $result['paymentUrl']);
    }

    public function test_client_create_payment_uses_hold_url_when_use_hold_true(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/hold' => Http::response(['token' => 't', 'paymentUrl' => 'u'], 200),
        ]);

        $this->makeClient()->createPayment(
            '1', 100.0, 'https://site.com/success', 'https://site.com/webhook',
            0, [], 'PP', true
        );

        Http::assertSent(fn ($r) => str_contains($r->url(), '/payment/hold'));
    }

    public function test_client_create_payment_uses_create_url_by_default(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/create' => Http::response(['token' => 't', 'paymentUrl' => 'u'], 200),
        ]);

        $this->makeClient()->createPayment('1', 100.0, 'https://site.com/success', 'https://site.com/webhook');

        Http::assertSent(fn ($r) => str_contains($r->url(), '/payment/create'));
    }

    public function test_client_create_payment_returns_null_on_api_failure(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/create' => Http::response([], 500),
        ]);

        $this->assertNull(
            $this->makeClient()->createPayment('1', 100.0, 'https://site.com/success', 'https://site.com/webhook')
        );
    }

    public function test_client_create_payment_sends_merchant_type_pp(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/create' => Http::response(['token' => 't', 'paymentUrl' => 'u'], 200),
        ]);

        $this->makeClient()->createPayment('1', 100.0, 'https://site.com/success', 'https://site.com/webhook');

        Http::assertSent(fn ($r) => $r->data()['merchantType'] === 'PP');
    }

    public function test_client_create_payment_sends_currency_980(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/create' => Http::response(['token' => 't', 'paymentUrl' => 'u'], 200),
        ]);

        $this->makeClient()->createPayment('1', 100.0, 'https://site.com/success', 'https://site.com/webhook');

        Http::assertSent(fn ($r) => $r->data()['currency'] === '980');
    }

    public function test_client_create_payment_sends_correct_signature(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/create' => Http::response(['token' => 't', 'paymentUrl' => 'u'], 200),
        ]);

        // Default products: [['name'=>'Order','count'=>1,'price'=>100.0]]
        // productsString = 'Order' . 1 . 10000 = 'Order110000'
        $responseUrl = 'https://site.com/webhook';
        $redirectUrl = 'https://site.com/success';

        $expected = base64_encode(sha1(
            self::PASSWORD . self::STORE_ID . '42' . '10000' . '980' . '2' . 'PP'
            . $responseUrl . $redirectUrl . 'Order110000' . self::PASSWORD,
            true,
        ));

        $this->makeClient()->createPayment('42', 100.0, $redirectUrl, $responseUrl, 2);

        Http::assertSent(fn ($r) => $r->data()['signature'] === $expected);
    }

    // ────────────────────────────────────────────────────────────
    // PrivatPayPartsClient — checkStatus
    // ────────────────────────────────────────────────────────────

    public function test_client_check_status_returns_object(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/state' => Http::response(['paymentState' => 'SUCCESS'], 200),
        ]);

        $result = $this->makeClient()->checkStatus('42');

        $this->assertNotNull($result);
        $this->assertEquals('SUCCESS', $result->paymentState);
    }

    public function test_client_check_status_returns_null_on_failure(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/state' => Http::response([], 404),
        ]);

        $this->assertNull($this->makeClient()->checkStatus('42'));
    }

    public function test_client_check_status_sends_correct_signature(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/state' => Http::response(['paymentState' => 'SUCCESS'], 200),
        ]);

        $expected = base64_encode(sha1(self::PASSWORD . self::STORE_ID . '42' . self::PASSWORD, true));

        $this->makeClient()->checkStatus('42');

        Http::assertSent(fn ($r) => $r->data()['signature'] === $expected);
    }

    // ────────────────────────────────────────────────────────────
    // PrivatPayPartsClient — confirm / cancel
    // ────────────────────────────────────────────────────────────

    public function test_client_confirm_returns_true_on_success_state(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/confirm' => Http::response(['state' => 'SUCCESS'], 200),
        ]);

        $this->assertTrue($this->makeClient()->confirm('42'));
    }

    public function test_client_confirm_sends_order_id_in_body(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/confirm' => Http::response(['state' => 'SUCCESS'], 200),
        ]);

        $this->makeClient()->confirm('42');

        Http::assertSent(fn ($r) => $r->data()['orderId'] === '42');
    }

    public function test_client_confirm_sends_correct_signature(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/confirm' => Http::response(['state' => 'SUCCESS'], 200),
        ]);

        $expected = base64_encode(sha1(self::PASSWORD . self::STORE_ID . '42' . self::PASSWORD, true));

        $this->makeClient()->confirm('42');

        Http::assertSent(fn ($r) => $r->data()['signature'] === $expected);
    }

    public function test_client_cancel_returns_true_on_success(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/cancel' => Http::response(['state' => 'MERCHANT_CANCEL'], 200),
        ]);

        $this->assertTrue($this->makeClient()->cancel('42'));
    }

    public function test_client_cancel_sends_order_id_in_body(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/cancel' => Http::response(['state' => 'MERCHANT_CANCEL'], 200),
        ]);

        $this->makeClient()->cancel('42');

        Http::assertSent(fn ($r) => $r->data()['orderId'] === '42');
    }

    // ────────────────────────────────────────────────────────────
    // PrivatPayPartsClient — decline (refund after success)
    // ────────────────────────────────────────────────────────────

    public function test_client_decline_returns_true_on_success(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/decline' => Http::response(['state' => 'SUCCESS'], 200),
        ]);

        $this->assertTrue($this->makeClient()->decline('42', 100.0));
    }

    public function test_client_decline_returns_false_on_failure(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/decline' => Http::response([], 500),
        ]);

        $this->assertFalse($this->makeClient()->decline('42', 100.0));
    }

    public function test_client_decline_sends_correct_signature(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/decline' => Http::response(['state' => 'SUCCESS'], 200),
        ]);

        // Signature: pwd + storeId + orderId + amountKopecks + pwd
        $expected = base64_encode(sha1(
            self::PASSWORD . self::STORE_ID . '42' . '10000' . self::PASSWORD,
            true,
        ));

        $this->makeClient()->decline('42', 100.0);

        Http::assertSent(fn ($r) => $r->data()['signature'] === $expected);
    }

    public function test_client_decline_sends_amount_in_body(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/decline' => Http::response(['state' => 'SUCCESS'], 200),
        ]);

        $this->makeClient()->decline('42', 150.5);

        Http::assertSent(fn ($r) => $r->data()['amount'] === 150.5);
    }

    // ────────────────────────────────────────────────────────────
    // PrivatPayPartsClient — verifyWebhookSignature
    // ────────────────────────────────────────────────────────────

    public function test_client_verifies_valid_webhook_signature(): void
    {
        $payload = $this->makeWebhookPayload('42', 'SUCCESS', 'Оплата успішна');

        $this->assertTrue($this->makeClient()->verifyWebhookSignature($payload));
    }

    public function test_client_rejects_invalid_webhook_signature(): void
    {
        $payload              = $this->makeWebhookPayload('42', 'SUCCESS', 'Оплата успішна');
        $payload['signature'] = 'invalid_sig';

        $this->assertFalse($this->makeClient()->verifyWebhookSignature($payload));
    }

    // ────────────────────────────────────────────────────────────
    // PrivatPayPartsClient — buildProductsString
    // ────────────────────────────────────────────────────────────

    public function test_client_builds_products_string_correctly(): void
    {
        $products = [
            ['name' => 'Товар А', 'count' => 2, 'price' => 50.0],
            ['name' => 'Товар Б', 'count' => 1, 'price' => 100.5],
        ];

        $result = $this->makeClient()->buildProductsString($products);

        // 'Товар А' . 2 . 5000 . 'Товар Б' . 1 . 10050
        $this->assertEquals('Товар А25000Товар Б110050', $result);
    }

    // ────────────────────────────────────────────────────────────
    // PrivatPayPartsGateway — init
    // ────────────────────────────────────────────────────────────

    public function test_gateway_init_returns_payment_url_and_token(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/create' => Http::response([
                'token'      => 'priv_tok',
                'paymentUrl' => self::BASE_URL . '/payment?token=priv_tok',
            ], 200),
        ]);

        $order  = $this->makeOrderWithCredentials();
        $result = app(PrivatPayPartsGateway::class)->init($order);

        $this->assertEquals('priv_tok', $result['token']);
        $this->assertStringContainsString('payparts2.privatbank.ua', $result['payment_url']);
    }

    public function test_gateway_init_returns_privat_order_id(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/create' => Http::response(['token' => 't', 'paymentUrl' => 'u'], 200),
        ]);

        $order  = $this->makeOrderWithCredentials();
        $result = app(PrivatPayPartsGateway::class)->init($order);

        $this->assertArrayHasKey('privat_order_id', $result);
        $this->assertStringStartsWith($order->id . '_', $result['privat_order_id']);
    }

    public function test_gateway_init_sends_privat_order_id_with_timestamp(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/create' => Http::response(['token' => 't', 'paymentUrl' => 'u'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        app(PrivatPayPartsGateway::class)->init($order);

        Http::assertSent(fn ($r) => str_starts_with($r->data()['orderId'], $order->id . '_'));
    }

    public function test_gateway_init_uses_installment_months_as_parts_count(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/create' => Http::response(['token' => 't', 'paymentUrl' => 'u'], 200),
        ]);

        $order                     = $this->makeOrderWithCredentials();
        $order->installment_months = 6;
        $order->save();

        app(PrivatPayPartsGateway::class)->init($order);

        Http::assertSent(fn ($r) => $r->data()['partsCount'] === 6);
    }

    public function test_gateway_init_uses_merchant_type_from_credentials(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/create' => Http::response(['token' => 't', 'paymentUrl' => 'u'], 200),
        ]);

        $payMethod = PayMethod::create([
            'title'              => json_encode(['uk' => 'PrivatInstallments']),
            'slug'               => 'privatinstallments_' . uniqid(),
            'gateway'            => 'privatpayparts',
            'is_active'          => true,
            'commission_percent' => 0,
            'priority'           => 1,
        ]);

        PaymentCredential::create([
            'pay_method_id' => $payMethod->id,
            'is_default'    => true,
            'credentials'   => [
                'merchant_id'   => self::STORE_ID,
                'password'      => self::PASSWORD,
                'merchant_type' => 'II',
            ],
        ]);

        $order = Order::create([
            'pay_method_id'   => $payMethod->id,
            'name'            => 'Test User',
            'phone'           => '+380000000000',
            'email'           => 'test@test.com',
            'address'         => 'Test address',
            'cost'            => 100.0,
            'order_status_id' => 1,
            'first_name'      => 'Test',
        ]);

        app(PrivatPayPartsGateway::class)->init($order);

        Http::assertSent(fn ($r) => $r->data()['merchantType'] === 'II');
    }

    // ────────────────────────────────────────────────────────────
    // PrivatPayPartsGateway — status
    // ────────────────────────────────────────────────────────────

    #[DataProvider('privatStateProvider')]
    public function test_status_maps_privat_state_correctly(string $state, string $expected): void
    {
        Http::fake([
            self::BASE_URL . '/payment/state' => Http::response(['paymentState' => $state], 200),
        ]);

        $order   = $this->makeOrderWithCredentials();
        $invoice = PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'initiated',
            'gateway_response' => ['token' => 'priv_tok'],
        ]);

        $this->assertEquals($expected, app(PrivatPayPartsGateway::class)->status($invoice));
    }

    /** @return array<string, array{string, string}> */
    public static function privatStateProvider(): array
    {
        return [
            'SUCCESS'         => ['SUCCESS', 'paid'],
            'LOCKED'          => ['LOCKED', 'hold'],
            'WAIT_CONFIRM'    => ['WAIT_CONFIRM', 'hold'],
            'PROCESSING'      => ['PROCESSING', 'processing'],
            'CLIENT_WAIT'     => ['CLIENT_WAIT', 'processing'],
            'RETURNED'        => ['RETURNED', 'refunded'],
            'FAIL'            => ['FAIL', 'failed'],
            'TIMEOUT'         => ['TIMEOUT', 'failed'],
            'CLIENT_CANCEL'   => ['CLIENT_CANCEL', 'failed'],
            'MERCHANT_CANCEL' => ['MERCHANT_CANCEL', 'failed'],
            'unknown'         => ['SOME_UNKNOWN', 'pending'],
        ];
    }

    public function test_status_uses_privat_order_id_from_gateway_response(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/state' => Http::response(['paymentState' => 'SUCCESS'], 200),
        ]);

        $order   = $this->makeOrderWithCredentials();
        $invoice = PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'initiated',
            'gateway_response' => ['privat_order_id' => '42_1620000000'],
        ]);

        app(PrivatPayPartsGateway::class)->status($invoice);

        Http::assertSent(fn ($r) => $r->data()['orderId'] === '42_1620000000');
    }

    // ────────────────────────────────────────────────────────────
    // PrivatPayPartsGateway — confirm
    // ────────────────────────────────────────────────────────────

    public function test_confirm_returns_true_for_success_with_valid_signature(): void
    {
        $order   = $this->makeOrderWithCredentials();
        $payload = $this->makeWebhookPayload((string) $order->id, 'SUCCESS', 'Оплата успішна');

        $this->assertTrue(app(PrivatPayPartsGateway::class)->confirm($payload));
    }

    public function test_confirm_returns_false_for_fail_state(): void
    {
        $order   = $this->makeOrderWithCredentials();
        $payload = $this->makeWebhookPayload((string) $order->id, 'FAIL', 'Помилка');

        $this->assertFalse(app(PrivatPayPartsGateway::class)->confirm($payload));
    }

    public function test_confirm_returns_false_for_invalid_signature(): void
    {
        $order              = $this->makeOrderWithCredentials();
        $payload            = $this->makeWebhookPayload((string) $order->id, 'SUCCESS', 'Оплата успішна');
        $payload['signature'] = 'invalid';

        $this->assertFalse(app(PrivatPayPartsGateway::class)->confirm($payload));
    }

    public function test_confirm_returns_false_when_order_not_found(): void
    {
        $payload = $this->makeWebhookPayload('999999', 'SUCCESS', 'OK');

        $this->assertFalse(app(PrivatPayPartsGateway::class)->confirm($payload));
    }

    public function test_confirm_returns_false_when_no_order_id(): void
    {
        $this->assertFalse(app(PrivatPayPartsGateway::class)->confirm([]));
    }

    public function test_confirm_handles_composite_order_id_from_real_webhook(): void
    {
        // orderId у реальному webhook = "{id}_{time}", (int) парсить числовий префікс
        $order   = $this->makeOrderWithCredentials();
        $orderId = $order->id . '_1620000000';
        $payload = $this->makeWebhookPayload($orderId, 'SUCCESS', 'OK');

        $this->assertTrue(app(PrivatPayPartsGateway::class)->confirm($payload));
    }

    // ────────────────────────────────────────────────────────────
    // PrivatPayPartsGateway — captureHold / releaseHold
    // ────────────────────────────────────────────────────────────

    public function test_capture_hold_calls_confirm_endpoint_with_order_id(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/confirm' => Http::response(['state' => 'SUCCESS'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();

        $this->assertTrue(app(PrivatPayPartsGateway::class)->captureHold($order));
        Http::assertSent(fn ($r) => str_contains($r->url(), '/payment/confirm')
            && $r->data()['orderId'] === (string) $order->id);
    }

    public function test_capture_hold_uses_privat_order_id_from_invoice(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/confirm' => Http::response(['state' => 'SUCCESS'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'hold',
            'gateway_response' => ['privat_order_id' => $order->id . '_1620000000'],
        ]);

        app(PrivatPayPartsGateway::class)->captureHold($order);

        Http::assertSent(fn ($r) => $r->data()['orderId'] === $order->id . '_1620000000');
    }

    public function test_release_hold_calls_cancel_endpoint_with_order_id(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/cancel' => Http::response(['state' => 'MERCHANT_CANCEL'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();

        $this->assertTrue(app(PrivatPayPartsGateway::class)->releaseHold($order));
        Http::assertSent(fn ($r) => str_contains($r->url(), '/payment/cancel')
            && $r->data()['orderId'] === (string) $order->id);
    }

    public function test_release_hold_uses_privat_order_id_from_invoice(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/cancel' => Http::response(['state' => 'MERCHANT_CANCEL'], 200),
        ]);

        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'hold',
            'gateway_response' => ['privat_order_id' => $order->id . '_1620000000'],
        ]);

        app(PrivatPayPartsGateway::class)->releaseHold($order);

        Http::assertSent(fn ($r) => $r->data()['orderId'] === $order->id . '_1620000000');
    }

    // ────────────────────────────────────────────────────────────
    // PrivatPayPartsGateway — return (refund after success)
    // ────────────────────────────────────────────────────────────

    public function test_gateway_return_calls_decline_endpoint(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/decline' => Http::response(['state' => 'SUCCESS'], 200),
        ]);

        $order = $this->makeOrderWithCredentials(100.0);
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'paid',
            'gateway_response' => ['privat_order_id' => $order->id . '_1620000000', 'amount' => 100.0],
        ]);

        $this->assertTrue(app(PrivatPayPartsGateway::class)->return($order));
        Http::assertSent(fn ($r) => str_contains($r->url(), '/payment/decline')
            && $r->data()['orderId'] === $order->id . '_1620000000');
    }

    public function test_gateway_return_fallsback_to_order_id_without_invoice(): void
    {
        Http::fake([
            self::BASE_URL . '/payment/decline' => Http::response(['state' => 'SUCCESS'], 200),
        ]);

        $order = $this->makeOrderWithCredentials(100.0);

        $this->assertTrue(app(PrivatPayPartsGateway::class)->return($order));
        Http::assertSent(fn ($r) => $r->data()['orderId'] === (string) $order->id);
    }

    // ────────────────────────────────────────────────────────────
    // PrivatPayPartsGateway — getInstallments / getCommission
    // ────────────────────────────────────────────────────────────

    public function test_get_installments_returns_four_periods(): void
    {
        $installments = app(PrivatPayPartsGateway::class)->getInstallments(1000.0);

        $this->assertCount(4, $installments);

        foreach ($installments as $item) {
            $this->assertArrayHasKey('months', $item);
            $this->assertArrayHasKey('monthly', $item);
            $this->assertArrayHasKey('total', $item);
            $this->assertGreaterThan($item['monthly'], $item['total']);
        }
    }

    public function test_get_installments_available_months_are_2_4_6_10(): void
    {
        $months = array_column(app(PrivatPayPartsGateway::class)->getInstallments(1000.0), 'months');

        $this->assertEquals([2, 4, 6, 10], $months);
    }

    public function test_get_commission_returns_positive_value(): void
    {
        $this->assertGreaterThan(0, app(PrivatPayPartsGateway::class)->getCommission(1000.0, 6));
    }

    // ────────────────────────────────────────────────────────────
    // WebhookProcessor — PrivatBank orderId format
    // ────────────────────────────────────────────────────────────

    public function test_webhook_processor_extracts_order_id_from_order_id_field(): void
    {
        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'initiated',
            'gateway_response' => ['token' => 'priv_tok'],
        ]);

        $payload   = $this->makeWebhookPayload((string) $order->id, 'SUCCESS', 'Оплата успішна');
        $processor = app(WebhookProcessor::class);
        $result    = $processor->process('privatpayparts', $payload);

        $this->assertTrue($result);
        $this->assertEquals('paid', $order->fresh()->paymentInvoices()->latest()->first()->status);
    }

    public function test_webhook_processor_extracts_order_from_composite_order_id(): void
    {
        // Реальний webhook використовує orderId = "{id}_{time}" — (int) парсить лише цифровий префікс
        $order = $this->makeOrderWithCredentials();
        PaymentInvoice::create([
            'order_id'         => $order->id,
            'amount'           => 100.0,
            'status'           => 'initiated',
            'gateway_response' => ['privat_order_id' => $order->id . '_1620000000'],
        ]);

        $payload   = $this->makeWebhookPayload($order->id . '_1620000000', 'SUCCESS', 'OK');
        $processor = app(WebhookProcessor::class);
        $result    = $processor->process('privatpayparts', $payload);

        $this->assertTrue($result);
    }

    // ────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────

    private function makeClient(): PrivatPayPartsClient
    {
        return new PrivatPayPartsClient(self::STORE_ID, self::PASSWORD);
    }

    private function makeOrderWithCredentials(float $cost = 100.0, float $delivery = 50.0): Order
    {
        $payMethod = PayMethod::create([
            'title'              => json_encode(['uk' => 'PrivatPayParts']),
            'slug'               => 'privatpayparts_' . uniqid(),
            'gateway'            => 'privatpayparts',
            'is_active'          => true,
            'commission_percent' => 0,
            'priority'           => 1,
        ]);

        PaymentCredential::create([
            'pay_method_id' => $payMethod->id,
            'is_default'    => true,
            'credentials'   => [
                'merchant_id' => self::STORE_ID,
                'password'    => self::PASSWORD,
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

    /**
     * Будує валідний webhook payload із правильним SHA1-Base64 підписом.
     * Підпис: base64(sha1(pwd+storeId+orderId+paymentState+message+pwd)).
     *
     * @return array<string, mixed>
     */
    private function makeWebhookPayload(string $orderId, string $paymentState, string $message): array
    {
        $signature = base64_encode(sha1(
            self::PASSWORD . self::STORE_ID . $orderId . $paymentState . $message . self::PASSWORD,
            true,
        ));

        return [
            'storeId'      => self::STORE_ID,
            'orderId'      => $orderId,
            'paymentState' => $paymentState,
            'message'      => $message,
            'signature'    => $signature,
        ];
    }
}
