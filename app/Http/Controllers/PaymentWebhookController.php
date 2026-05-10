<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPaymentWebhook;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use App\Services\Payment\GatewayRegistry;
use App\Services\Payment\Gateways\PayLink\PayLinkClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        private readonly GatewayRegistry $registry,
        private readonly CredentialResolver $credentials,
    ) {}

    /**
     * Загальний обробник: відповідаємо HTTP 200 завжди.
     */
    public function handle(Request $request, string $gateway): Response
    {
        try {
            $payload = $request->all();

            if (! $this->registry->has($gateway)) {
                Log::channel('payments')->warning('Webhook: unknown gateway', ['gateway' => $gateway]);

                return response('', 200);
            }

            ProcessPaymentWebhook::dispatch($gateway, $payload);
        } catch (\Throwable $e) {
            Log::channel('payments')->error('Webhook: dispatch failed', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);
        }

        return response('', 200);
    }

    /**
     * Спеціальний обробник PayLink: викликає getTranState(sid),
     * збагачує payload {order_id=TRANID, resultCode} і диспатчить Job.
     */
    public function handlePayLink(Request $request): Response
    {
        try {
            $sid = $request->input('sid');

            if (! $sid) {
                Log::channel('payments')->warning('PayLink webhook: missing sid');

                return response('', 200);
            }

            $enrichedPayload = $this->buildPayLinkPayload($sid);

            if ($enrichedPayload !== null) {
                ProcessPaymentWebhook::dispatch('paylink', $enrichedPayload);
            }
        } catch (\Throwable $e) {
            Log::channel('payments')->error('PayLink webhook: error', [
                'error' => $e->getMessage(),
            ]);
        }

        return response('', 200);
    }

    /**
     * Викликає getTranState, витягує order_id з TRANID та повертає збагачений payload.
     *
     * @return array<string, mixed>|null
     */
    private function buildPayLinkPayload(string $sid): ?array
    {
        $invoice = PaymentInvoice::query()
            ->whereJsonContains('gateway_response->sid', $sid)
            ->latest()
            ->first();

        if (! $invoice) {
            Log::channel('payments')->warning('PayLink webhook: invoice not found for sid', ['sid' => $sid]);

            return null;
        }

        $payMethodId = $invoice->order?->pay_method_id;
        if (! $payMethodId) {
            return null;
        }

        $creds = $this->credentials->resolve($payMethodId);
        $client = new PayLinkClient(
            merchantId: $creds['merchant_id'] ?? '',
            privateKey: $creds['private_key'] ?? '',
            apiUrl: config('services.paylink.api_base_url', 'https://api-inst4.paylink.com.ua'),
            frameUrl: config('services.paylink.frame_base_url', 'https://inst4.paylink.com.ua'),
        );

        $tranState = $client->getTranState($sid);

        if (! $tranState) {
            Log::channel('payments')->warning('PayLink webhook: getTranState failed', ['sid' => $sid]);

            return null;
        }

        $resultCode = $tranState['resultCode'] ?? null;
        $tranId = $tranState['oper_data'][0]['TRANID'] ?? null;

        if (! $resultCode || ! $tranId) {
            Log::channel('payments')->warning('PayLink webhook: missing resultCode/TRANID', [
                'sid' => $sid,
                'tranState' => $tranState,
            ]);

            return null;
        }

        return [
            'order_id' => $tranId,
            'resultCode' => $resultCode,
            'sid' => $sid,
        ];
    }
}
