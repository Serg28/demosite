<?php

namespace App\Services\Payment\Gateways\LiqPay;

use App\Contracts\Holdable;
use App\Contracts\PaymentGatewayInterface;
use App\Contracts\Returnable;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use Illuminate\Support\Facades\Log;

class LiqPayGateway implements Holdable, PaymentGatewayInterface, Returnable
{
    public function __construct(private readonly CredentialResolver $credentials) {}

    /**
     * Ініціалізує платіж LiqPay.
     * Тип операції (hold/pay) визначається полем use_hold на PayMethod.
     *
     * @return array{data: string, signature: string, checkout_url: string, liqpay_order_id: string}
     */
    public function init(Order $order): array
    {
        $order->loadMissing('payMethod');
        $client = $this->makeClient($order->pay_method_id);
        $liqpayOrder = $order->id.'_'.uniqid('', true);
        $action = ($order->payMethod?->use_hold ?? false) ? 'hold' : 'pay';

        return array_merge(
            $client->buildFormData([
                'action' => $action,
                'amount' => $order->getTotalCost(),
                'currency' => 'UAH',
                'description' => __t('Оплата замовлення № ').$order->id,
                'order_id' => $liqpayOrder,
                'result_url' => route('checkout.success', $order),
                'server_url' => url('/payment/webhook/liqpay'),
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
        $client = $this->makeClient($invoice->order->pay_method_id);
        $response = $client->api('status', ['order_id' => $liqpayOrderId]);

        return $this->mapStatus($response?->status ?? '');
    }

    /**
     * Підтверджує webhook від LiqPay: перевіряє підпис, повертає true для success/hold_wait.
     *
     * @param  array{data: string, signature: string}  $payload
     */
    public function confirm(array $payload): bool
    {
        $data = $payload['data'] ?? '';
        $signature = $payload['signature'] ?? '';

        if (! $data || ! $signature) {
            return false;
        }

        $decoded = json_decode(base64_decode($data), true) ?? [];
        $orderId = (int) explode('_', $decoded['order_id'] ?? '0')[0];
        $payMethodId = Order::find($orderId)?->pay_method_id;

        if (! $payMethodId) {
            return false;
        }

        $client = $this->makeClient($payMethodId);

        if (! $client->verifySignature($data, $signature)) {
            Log::warning('LiqPay: invalid webhook signature', ['order_id' => $decoded['order_id'] ?? null]);

            return false;
        }

        return in_array($decoded['status'] ?? '', ['success', 'hold_wait'], true);
    }

    /**
     * Підтверджує захоплення заблокованих коштів (hold → capture).
     */
    public function captureHold(Order $order): bool
    {
        $liqpayOrderId = $this->resolveLiqpayOrderId($order);

        if (! $liqpayOrderId) {
            return false;
        }

        $client = $this->makeClient($order->pay_method_id);
        $response = $client->api('capture', [
            'order_id' => $liqpayOrderId,
            'amount' => $order->getTotalCost(),
            'currency' => 'UAH',
        ]);

        return ($response?->status ?? '') === 'success';
    }

    /**
     * Скасовує холд — повертає кошти покупцю.
     */
    public function releaseHold(Order $order): bool
    {
        $liqpayOrderId = $this->resolveLiqpayOrderId($order);

        if (! $liqpayOrderId) {
            return false;
        }

        $client = $this->makeClient($order->pay_method_id);
        $response = $client->api('cancel', [
            'order_id' => $liqpayOrderId,
            'amount' => $order->getTotalCost(),
            'currency' => 'UAH',
        ]);

        return ($response?->status ?? '') === 'success';
    }

    /**
     * Повертає кошти (refund) після успішної оплати.
     */
    public function return(Order $order): bool
    {
        $liqpayOrderId = $this->resolveLiqpayOrderId($order);

        if (! $liqpayOrderId) {
            return false;
        }

        $client = $this->makeClient($order->pay_method_id);
        $response = $client->api('refund', [
            'order_id' => $liqpayOrderId,
            'amount' => $order->getTotalCost(),
            'currency' => 'UAH',
        ]);

        return ($response?->status ?? '') === 'success';
    }

    private function makeClient(int $payMethodId): LiqPayClient
    {
        $creds = $this->credentials->resolve($payMethodId);

        return new LiqPayClient(
            publicKey: $creds['public_key'] ?? '',
            privateKey: $creds['private_key'] ?? '',
        );
    }

    private function resolveLiqpayOrderId(Order $order): ?string
    {
        return $order->paymentInvoices()
            ->latest()
            ->first()
            ?->gateway_response['liqpay_order_id'] ?? null;
    }

    private function mapStatus(string $liqpayStatus): string
    {
        return match ($liqpayStatus) {
            'success' => 'paid',
            'hold_wait' => 'hold',
            'processing' => 'processing',
            'failure', 'error' => 'failed',
            default => 'pending',
        };
    }
}
