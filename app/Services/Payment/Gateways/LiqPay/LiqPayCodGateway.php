<?php

namespace App\Services\Payment\Gateways\LiqPay;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use Illuminate\Support\Facades\Log;

/**
 * LiqPay Cash on Delivery — передоплата доставки, залишок при отриманні.
 */
class LiqPayCodGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly CredentialResolver $credentials) {}

    /**
     * Ініціалізує pay-платіж (без холду) для передоплати вартості доставки.
     *
     * @return array{data: string, signature: string, checkout_url: string, liqpay_order_id: string}
     */
    public function init(Order $order): array
    {
        $client      = $this->makeClient($order->pay_method_id);
        $liqpayOrder = $order->id . '_' . uniqid('', true);
        $amount      = (float) ($order->price_delivery ?: $order->getTotalCost());

        return array_merge(
            $client->buildFormData([
                'action'      => 'pay',
                'amount'      => $amount,
                'currency'    => 'UAH',
                'description' => __t('Оплата транспортних послуг на сайті') . ' ' . config('app.name'),
                'order_id'    => $liqpayOrder,
                'result_url'  => route('checkout.success', $order),
                'server_url'  => url('/payment/webhook/liqpay_cod'),
            ]),
            ['liqpay_order_id' => $liqpayOrder],
        );
    }

    /**
     * Перевіряє статус оплати через LiqPay API.
     */
    public function status(PaymentInvoice $invoice): string
    {
        $liqpayOrderId = $invoice->gateway_response['liqpay_order_id'] ?? null;

        if (! $liqpayOrderId) {
            return 'pending';
        }

        $invoice->loadMissing('order');
        $client   = $this->makeClient($invoice->order->pay_method_id);
        $response = $client->api('status', ['order_id' => $liqpayOrderId]);

        return match ($response?->status ?? '') {
            'success'          => 'paid',
            'processing'       => 'processing',
            'failure', 'error' => 'failed',
            default            => 'pending',
        };
    }

    /**
     * Підтверджує webhook від LiqPay: перевіряє підпис, повертає true тільки для success.
     *
     * @param  array{data: string, signature: string}  $payload
     */
    public function confirm(array $payload): bool
    {
        $data      = $payload['data'] ?? '';
        $signature = $payload['signature'] ?? '';

        if (! $data || ! $signature) {
            return false;
        }

        $decoded     = json_decode(base64_decode($data), true) ?? [];
        $orderId     = (int) explode('_', $decoded['order_id'] ?? '0')[0];
        $payMethodId = Order::find($orderId)?->pay_method_id;

        if (! $payMethodId) {
            return false;
        }

        $client = $this->makeClient($payMethodId);

        if (! $client->verifySignature($data, $signature)) {
            Log::warning('LiqPayCod: invalid webhook signature', ['order_id' => $decoded['order_id'] ?? null]);

            return false;
        }

        return ($decoded['status'] ?? '') === 'success';
    }

    private function makeClient(int $payMethodId): LiqPayClient
    {
        $creds = $this->credentials->resolve($payMethodId);

        return new LiqPayClient(
            publicKey:  $creds['public_key'] ?? '',
            privateKey: $creds['private_key'] ?? '',
        );
    }
}
