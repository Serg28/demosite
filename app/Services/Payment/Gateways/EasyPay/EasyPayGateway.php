<?php

namespace App\Services\Payment\Gateways\EasyPay;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use Illuminate\Support\Facades\Log;

class EasyPayGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly CredentialResolver $credentials) {}

    /**
     * Ініціалізує платіж EasyPay (2 кроки: createApp → createOrder).
     * Повертає URL для редіректу або порожній рядок при помилці.
     */
    public function init(Order $order): string
    {
        $client = $this->makeClient($order->pay_method_id);

        $app = $client->createApp();

        if (! $app || isset($app->error)) {
            Log::channel('payments')->error('EasyPay: createApp failed', [
                'orderId' => $order->id,
                'error' => $app?->error ?? 'null response',
            ]);

            return '';
        }

        $body = $client->buildBody($order);
        $result = $client->createOrder($app->appId, $app->pageId, $body);

        if (! $result || isset($result->error)) {
            Log::channel('payments')->error('EasyPay: createOrder failed', [
                'orderId' => $order->id,
                'error' => $result?->error ?? 'null response',
            ]);

            return '';
        }

        return $result->forwardUrl ?? '';
    }

    /**
     * Перевіряє поточний статус через EasyPay API.
     */
    public function status(PaymentInvoice $invoice): string
    {
        $orderId = $invoice->gateway_response['orderId'] ?? null;

        if (! $orderId) {
            return 'pending';
        }

        $invoice->loadMissing('order');
        $client = $this->makeClient($invoice->order->pay_method_id);
        $response = $client->getOrder((string) $orderId);

        return $this->mapStatus($response?->status ?? '');
    }

    /**
     * Підтверджує webhook: orderId присутній та error = null.
     *
     * @param  array<string, mixed>  $payload
     */
    public function confirm(array $payload): bool
    {
        $orderId = $payload['orderId'] ?? null;

        if (! $orderId) {
            return false;
        }

        return ! isset($payload['error']) || $payload['error'] === null;
    }

    private function makeClient(int $payMethodId): EasyPayClient
    {
        $creds = $this->credentials->resolve($payMethodId);

        return new EasyPayClient(
            partnerKey: $creds['partner_key'] ?? '',
            serviceKey: $creds['service_key'] ?? '',
            secretKey: $creds['secret_key'] ?? '',
        );
    }

    private function mapStatus(string $status): string
    {
        return match (strtoupper($status)) {
            'SUCCESS', 'PAID' => 'paid',
            'PROCESSING' => 'processing',
            'FAIL', 'ERROR' => 'failed',
            'REFUND' => 'refunded',
            default => 'pending',
        };
    }
}
