<?php

namespace Tests\Feature\Checkout;

use App\Jobs\ProcessPaymentWebhook;
use App\Models\Order;
use App\Models\PaymentCredential;
use App\Models\PaymentInvoice;
use App\Models\PayMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PaymentWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    // ────────────────────────────────────────────────────────────
    // Generic webhook /payment/webhook/{gateway}
    // ────────────────────────────────────────────────────────────

    public function test_webhook_returns_200_for_known_gateway(): void
    {
        Queue::fake();

        $response = $this->postJson('/payment/webhook/liqpay', ['data' => 'test', 'signature' => 'sig']);

        $response->assertStatus(200);
    }

    public function test_webhook_returns_200_for_unknown_gateway(): void
    {
        Queue::fake();

        $response = $this->postJson('/payment/webhook/nonexistent_gateway_xyz', []);

        $response->assertStatus(200);
    }

    public function test_webhook_dispatches_job_for_known_gateway(): void
    {
        Queue::fake();

        $this->postJson('/payment/webhook/liqpay', ['data' => 'abc', 'signature' => 'xyz']);

        Queue::assertPushed(ProcessPaymentWebhook::class, function ($job) {
            return $job->gatewaySlug === 'liqpay'
                && $job->payload === ['data' => 'abc', 'signature' => 'xyz'];
        });
    }

    public function test_webhook_does_not_dispatch_job_for_unknown_gateway(): void
    {
        Queue::fake();

        $this->postJson('/payment/webhook/nonexistent_xyz', ['foo' => 'bar']);

        Queue::assertNotPushed(ProcessPaymentWebhook::class);
    }

    public function test_webhook_dispatches_to_payments_queue(): void
    {
        Queue::fake();

        $this->postJson('/payment/webhook/monopay', ['reference' => '42', 'status' => 'success']);

        Queue::assertPushedOn('payments', ProcessPaymentWebhook::class);
    }

    public function test_webhook_returns_200_even_if_payload_empty(): void
    {
        Queue::fake();

        $response = $this->post('/payment/webhook/liqpay', []);

        $response->assertStatus(200);
    }

    public function test_webhook_accepts_post_without_csrf_token(): void
    {
        Queue::fake();

        // POST без X-CSRF-Token — не повинно повернути 419
        $response = $this->post('/payment/webhook/liqpay', ['test' => 1]);

        $response->assertStatus(200);
    }

    // ────────────────────────────────────────────────────────────
    // PayLink special handler /payment/webhook/paylink
    // ────────────────────────────────────────────────────────────

    public function test_paylink_webhook_returns_200_always(): void
    {
        Queue::fake();

        $response = $this->postJson('/payment/webhook/paylink', ['sid' => 'test-sid-123']);

        $response->assertStatus(200);
    }

    public function test_paylink_webhook_returns_200_without_sid(): void
    {
        Queue::fake();

        $response = $this->postJson('/payment/webhook/paylink', []);

        $response->assertStatus(200);
        Queue::assertNotPushed(ProcessPaymentWebhook::class);
    }

    public function test_paylink_webhook_dispatches_job_with_enriched_payload(): void
    {
        Queue::fake();

        Http::fake([
            'https://api-inst4.paylink.com.ua' => Http::response([
                'resultCode' => '100',
                'oper_data' => [['TRANID' => '42', 'AMOUNT' => '150.00']],
            ], 200),
        ]);

        $order = $this->makePayLinkOrderWithInvoice('session-abc-123');

        $this->postJson('/payment/webhook/paylink', ['sid' => 'session-abc-123']);

        Queue::assertPushed(ProcessPaymentWebhook::class, function ($job) {
            return $job->gatewaySlug === 'paylink'
                && $job->payload['order_id'] === '42'
                && $job->payload['resultCode'] === '100'
                && $job->payload['sid'] === 'session-abc-123';
        });
    }

    public function test_paylink_webhook_no_dispatch_when_invoice_not_found(): void
    {
        Queue::fake();

        $this->postJson('/payment/webhook/paylink', ['sid' => 'unknown-sid-999']);

        Queue::assertNotPushed(ProcessPaymentWebhook::class);
    }

    public function test_paylink_webhook_no_dispatch_when_tran_state_fails(): void
    {
        Queue::fake();

        Http::fake([
            'https://api-inst4.paylink.com.ua' => Http::response([], 500),
        ]);

        $order = $this->makePayLinkOrderWithInvoice('session-fail-123');

        $this->postJson('/payment/webhook/paylink', ['sid' => 'session-fail-123']);

        Queue::assertNotPushed(ProcessPaymentWebhook::class);
    }

    public function test_paylink_webhook_no_dispatch_when_result_code_missing(): void
    {
        Queue::fake();

        Http::fake([
            'https://api-inst4.paylink.com.ua' => Http::response(['foo' => 'bar'], 200),
        ]);

        $this->makePayLinkOrderWithInvoice('session-no-code-123');

        $this->postJson('/payment/webhook/paylink', ['sid' => 'session-no-code-123']);

        Queue::assertNotPushed(ProcessPaymentWebhook::class);
    }

    // ────────────────────────────────────────────────────────────
    // ProcessPaymentWebhook Job
    // ────────────────────────────────────────────────────────────

    public function test_job_has_correct_tries_and_backoff(): void
    {
        $job = new ProcessPaymentWebhook('liqpay', ['test' => 1]);

        $this->assertEquals(3, $job->tries);
        $this->assertEquals([60, 300, 900], $job->backoff);
    }

    public function test_job_is_dispatched_to_payments_queue(): void
    {
        $job = new ProcessPaymentWebhook('monopay', ['reference' => '42']);

        $this->assertEquals('payments', $job->queue);
    }

    // ────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────

    private function makePayLinkOrderWithInvoice(string $sid, float $cost = 100.0): Order
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
                'merchant_id' => 'test_merchant',
                'private_key' => 'test_private_key_secret',
            ],
        ]);

        $order = Order::create([
            'pay_method_id' => $payMethod->id,
            'name' => 'Test User',
            'phone' => '+380000000000',
            'email' => 'test@test.com',
            'address' => 'Test address',
            'cost' => $cost,
            'price_delivery' => 0,
            'order_status_id' => 1,
            'first_name' => 'Test',
        ]);

        PaymentInvoice::create([
            'order_id' => $order->id,
            'amount' => $cost,
            'status' => 'initiated',
            'gateway_response' => ['sid' => $sid],
        ]);

        return $order;
    }
}
