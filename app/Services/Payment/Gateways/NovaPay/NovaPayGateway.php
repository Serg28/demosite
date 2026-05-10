<?php

namespace App\Services\Payment\Gateways\NovaPay;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use Illuminate\Support\Facades\Log;

class NovaPayGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly CredentialResolver $credentials) {}

    /**
     * Ініціалізує платіж NovaPay (2 кроки: createCheckoutSession → addCheckoutPayment).
     * Повертає URL для редіректу або порожній рядок при помилці.
     */
    public function init(Order $order): string
    {
        $client = $this->makeClient($order->pay_method_id);

        // Крок 1: реєстрація сесії
        $session = $client->createCheckoutSession([
            'merchant_id' => $client->getMerchantId(),
            'callback_url' => url('/payment/webhook/novapay'),
            'success_url' => route('checkout.success', $order),
            'fail_url' => url('/payment/failed'),
            'delivery' => [
                'volume_weight' => 0.0004,
                'weight' => 0.1,
            ],
        ]);

        if (! $session || ! isset($session['id'])) {
            Log::channel('payments')->error('NovaPay: createCheckoutSession failed', [
                'orderId' => $order->id,
            ]);

            return '';
        }

        // Крок 2: прикріплення платежу до сесії
        $payment = $client->addCheckoutPayment([
            'merchant_id' => $client->getMerchantId(),
            'session_id' => $session['id'],
            'amount' => $order->getTotalCost(),
            'external_id' => $order->id,
            'use_hold' => false,
            'products' => [
                [
                    'count' => 1,
                    'price' => $order->getTotalCost(),
                    'description' => __t('Замовлення').' #'.$order->id,
                ],
            ],
        ]);

        if (! $payment || ! isset($payment['url'])) {
            Log::channel('payments')->error('NovaPay: addCheckoutPayment failed', [
                'orderId' => $order->id,
                'sessionId' => $session['id'],
            ]);

            return '';
        }

        return $payment['url'];
    }

    /**
     * Перевіряє статус за даними з інвойсу.
     * NovaPay не має окремого API перевірки статусу — статус відомий з webhook.
     */
    public function status(PaymentInvoice $invoice): string
    {
        $status = $invoice->gateway_response['status'] ?? null;

        if (! $status) {
            return 'pending';
        }

        return $this->mapStatus($status);
    }

    /**
     * Підтверджує webhook NovaPay.
     * Перевіряє external_id (числовий) та статус = 'paid'.
     *
     * @param  array<string, mixed>  $payload
     */
    public function confirm(array $payload): bool
    {
        // external_id може бути на верхньому рівні або всередині payments[0]
        $externalId = $payload['external_id']
            ?? $payload['payments'][0]['external_id']
            ?? null;

        if (! $externalId || ! is_numeric($externalId)) {
            Log::channel('payments')->warning('NovaPay: external_id missing or non-numeric in webhook', [
                'payload_keys' => array_keys($payload),
            ]);

            return false;
        }

        if (! isset($payload['status'])) {
            return false;
        }

        return strtolower($payload['status']) === 'paid';
    }

    private function makeClient(int $payMethodId): NovaPayClient
    {
        $creds = $this->credentials->resolve($payMethodId);

        return new NovaPayClient(
            merchantId: $creds['merchant_id'] ?? '',
            privateKey: $creds['private_key'] ?? '',
            apiUrl: config('services.novapay.api_base_url', 'https://api-ecom.novapay.ua/v1'),
        );
    }

    private function mapStatus(string $status): string
    {
        return match (strtolower($status)) {
            'paid' => 'paid',
            'processing' => 'processing',
            'hold' => 'hold',
            'expired', 'declined',
            'failed', 'error' => 'failed',
            default => 'pending',
        };
    }
}
