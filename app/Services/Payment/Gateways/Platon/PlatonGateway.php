<?php

namespace App\Services\Payment\Gateways\Platon;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use Illuminate\Support\Facades\Log;

class PlatonGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly CredentialResolver $credentials) {}

    /**
     * Ініціалізує платіж Platon.
     * Повертає дані форми для POST-редіректу на {apiUrl}/payment/auth.
     *
     * @return array<string, mixed>
     */
    public function init(Order $order): array
    {
        $client = $this->makeClient($order->pay_method_id);

        return $client->buildFormData([
            'order' => (string) $order->id,
            'amount' => $order->getTotalCost(),
            'description' => __t('Замовлення').' #'.$order->id,
            'url' => url('/payment/webhook/platon'),
            'email' => $order->email ?? '',
            'phone' => $order->phone ?? '',
        ]);
    }

    /**
     * Перевіряє поточний статус транзакції через Platon API.
     */
    public function status(PaymentInvoice $invoice): string
    {
        $orderId = $invoice->gateway_response['order'] ?? null;

        if (! $orderId) {
            return 'pending';
        }

        $invoice->loadMissing('order');
        $client = $this->makeClient($invoice->order->pay_method_id);
        $response = $client->checkStatus((string) $orderId);

        return $this->mapStatus($response?->status ?? '');
    }

    /**
     * Підтверджує callback від Platon: верифікує підпис і перевіряє статус.
     * Payload: email, order, card, sign, status.
     * order = order ID (наш ID замовлення).
     *
     * @param  array<string, mixed>  $payload
     */
    public function confirm(array $payload): bool
    {
        $orderId = $payload['order'] ?? null;

        if (! $orderId) {
            return false;
        }

        $order = Order::find((int) $orderId);

        if (! $order) {
            return false;
        }

        $client = $this->makeClient($order->pay_method_id);

        if (! $client->verifyCallbackSignature($payload)) {
            Log::channel('payments')->warning('Platon: invalid callback signature', [
                'order' => $orderId,
            ]);

            return false;
        }

        // Platon надсилає статус у полі status (SALE = успішна оплата)
        return strtoupper($payload['status'] ?? '') === 'SALE';
    }

    private function makeClient(int $payMethodId): PlatonClient
    {
        $creds = $this->credentials->resolve($payMethodId);

        return new PlatonClient(
            key: $creds['key'] ?? '',
            password: $creds['password'] ?? '',
            apiUrl: config('services.platon.api_base_url', 'https://secure.platononline.com'),
        );
    }

    private function mapStatus(string $status): string
    {
        return match (strtoupper($status)) {
            'SALE' => 'paid',
            'CHARGEBACK', 'REFUND' => 'refunded',
            'VOID' => 'cancelled',
            'PENDING', 'PROCESSING' => 'processing',
            'FAIL', 'DECLINED', 'ERROR' => 'failed',
            default => 'pending',
        };
    }
}
