<?php

namespace App\Services\Payment\Gateways\PayLink;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use Illuminate\Support\Facades\Log;

class PayLinkGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly CredentialResolver $credentials) {}

    /**
     * Ініціалізує PayLink-сесію та повертає URL для widget/iframe.
     * Webhook controller (Phase 4.6) викликає getTranState по sid та передає
     * збагачений payload {order_id, status, sid} до WebhookProcessor.
     */
    public function init(Order $order): string
    {
        $client = $this->makeClient($order->pay_method_id);
        $session = $client->getSession([
            'tranid' => (string) $order->id,
            'amount' => $order->getTotalCost(),
            'description' => __t('Замовлення').' #'.$order->id,
            'callback' => url('/payment/webhook/paylink'),
        ]);

        if (! $session || ! isset($session['sid'])) {
            Log::channel('payments')->error('PayLink: getSession failed', [
                'orderId' => $order->id,
            ]);

            return '';
        }

        return $client->getPaymentFrameUrl($session['sid']);
    }

    /**
     * Перевіряє статус за збереженим sid.
     * Webhook controller збагачує payload перед викликом WebhookProcessor::process().
     */
    public function status(PaymentInvoice $invoice): string
    {
        $resultCode = $invoice->gateway_response['resultCode'] ?? null;

        if ($resultCode === null) {
            return 'pending';
        }

        return $resultCode === '100' ? 'paid' : 'failed';
    }

    /**
     * Підтверджує webhook PayLink.
     * Очікує збагачений payload від webhook controller (Phase 4.6):
     * {order_id, status='paid', resultCode='100', ...}.
     *
     * @param  array<string, mixed>  $payload
     */
    public function confirm(array $payload): bool
    {
        if (! isset($payload['order_id'], $payload['resultCode'])) {
            return false;
        }

        if (! is_numeric($payload['order_id'])) {
            Log::channel('payments')->warning('PayLink: invalid order_id in webhook', [
                'order_id' => $payload['order_id'],
            ]);

            return false;
        }

        return $payload['resultCode'] === '100';
    }

    private function makeClient(int $payMethodId): PayLinkClient
    {
        $creds = $this->credentials->resolve($payMethodId);

        return new PayLinkClient(
            merchantId: $creds['merchant_id'] ?? '',
            privateKey: $creds['private_key'] ?? '',
            apiUrl: config('services.paylink.api_base_url', 'https://api-inst4.paylink.com.ua'),
            frameUrl: config('services.paylink.frame_base_url', 'https://inst4.paylink.com.ua'),
        );
    }
}
