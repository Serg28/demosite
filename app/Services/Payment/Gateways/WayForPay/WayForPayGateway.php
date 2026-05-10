<?php

namespace App\Services\Payment\Gateways\WayForPay;

use App\Contracts\Holdable;
use App\Contracts\PaymentGatewayInterface;
use App\Contracts\Returnable;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use Illuminate\Support\Facades\Log;

class WayForPayGateway implements Holdable, PaymentGatewayInterface, Returnable
{
    public function __construct(private readonly CredentialResolver $credentials) {}

    /**
     * Ініціалізує платіж WayForPay.
     * Тип транзакції (HOLD/PURCHASE) визначається полем use_hold на PayMethod.
     * productName/productCount/productPrice — реальний список товарів замовлення.
     * Повертає дані для рендеру форми POST на сторінку WayForPay.
     *
     * @return array<string, mixed>
     */
    public function init(Order $order): array
    {
        $order->loadMissing(['payMethod', 'products']);
        $client          = $this->makeClient($order->pay_method_id);
        $transactionType = ($order->payMethod?->use_hold ?? false) ? 'HOLD' : 'PURCHASE';

        [$productNames, $productCounts, $productPrices] = $this->buildProductArrays($order);

        return $client->buildFormData([
            'orderReference'  => (string) $order->id,
            'orderDate'       => time(),
            'amount'          => $order->getTotalCost(),
            'currency'        => 'UAH',
            'orderTimeout'    => 49000,
            'productName'     => $productNames,
            'productCount'    => $productCounts,
            'productPrice'    => $productPrices,
            'transactionType' => $transactionType,
            'serviceUrl'      => url('/payment/webhook/wayforpay'),
            'returnUrl'       => route('checkout.success', $order),
        ]);
    }

    /**
     * Формує паралельні масиви назв, кількостей і цін для WayForPay.
     *
     * @return array{0: string[], 1: int[], 2: float[]}
     */
    private function buildProductArrays(Order $order): array
    {
        if ($order->products->isNotEmpty()) {
            $names  = [];
            $counts = [];
            $prices = [];

            foreach ($order->products as $p) {
                $names[]  = $this->sanitizeProductName($p->title ?? __t('Товар'));
                $counts[] = (int) $p->count;
                $prices[] = (float) $p->price;
            }

            return [$names, $counts, $prices];
        }

        return [
            [__t('Замовлення № ').$order->id],
            [1],
            [$order->getTotalCost()],
        ];
    }

    private function sanitizeProductName(string $name): string
    {
        return str_replace(["'", '"', '&#39;', '&'], '', htmlspecialchars_decode($name));
    }

    /**
     * Перевіряє поточний статус транзакції через WayForPay API.
     */
    public function status(PaymentInvoice $invoice): string
    {
        $orderReference = $invoice->gateway_response['orderReference'] ?? null;

        if (! $orderReference) {
            return 'pending';
        }

        $invoice->loadMissing('order');
        $client = $this->makeClient($invoice->order->pay_method_id);
        $response = $client->checkStatus($orderReference);

        return $this->mapStatus($response?->transactionStatus ?? '');
    }

    /**
     * Підтверджує webhook від WayForPay: верифікує HMAC-MD5 підпис, повертає true для Approved.
     *
     * @param  array<string, mixed>  $payload
     */
    public function confirm(array $payload): bool
    {
        $orderReference = $payload['orderReference'] ?? null;

        if (! $orderReference) {
            return false;
        }

        $order = Order::find((int) $orderReference);

        if (! $order) {
            return false;
        }

        $client = $this->makeClient($order->pay_method_id);

        if (! $client->verifyWebhookSignature($payload)) {
            Log::channel('payments')->warning('WayForPay: invalid webhook signature', [
                'orderReference' => $orderReference,
            ]);

            return false;
        }

        return ($payload['transactionStatus'] ?? '') === 'Approved';
    }

    /**
     * Підтверджує захоплення заблокованих коштів (HOLD → CAPTURE).
     */
    public function captureHold(Order $order): bool
    {
        $orderReference = $this->resolveOrderReference($order);

        if (! $orderReference) {
            return false;
        }

        $client = $this->makeClient($order->pay_method_id);
        $response = $client->capture($orderReference, $order->getTotalCost());

        return ($response?->transactionStatus ?? '') === 'Approved';
    }

    /**
     * Скасовує холд — VOID.
     */
    public function releaseHold(Order $order): bool
    {
        $orderReference = $this->resolveOrderReference($order);

        if (! $orderReference) {
            return false;
        }

        $client = $this->makeClient($order->pay_method_id);
        $response = $client->void($orderReference);

        return ($response?->transactionStatus ?? '') === 'Voided';
    }

    /**
     * Повертає кошти (REFUND).
     */
    public function return(Order $order): bool
    {
        $orderReference = $this->resolveOrderReference($order);

        if (! $orderReference) {
            return false;
        }

        $client = $this->makeClient($order->pay_method_id);
        $response = $client->refund($orderReference, $order->getTotalCost());

        return ($response?->transactionStatus ?? '') === 'Refunded';
    }

    private function makeClient(int $payMethodId): WayForPayClient
    {
        $creds = $this->credentials->resolve($payMethodId);

        return new WayForPayClient(
            merchantAccount: $creds['merchant_account'] ?? '',
            merchantSecret: $creds['merchant_secret'] ?? '',
            merchantDomain: $creds['merchant_domain'] ?? '',
        );
    }

    private function resolveOrderReference(Order $order): ?string
    {
        return $order->paymentInvoices()
            ->latest()
            ->first()
            ?->gateway_response['orderReference'] ?? null;
    }

    private function mapStatus(string $wfpStatus): string
    {
        return match ($wfpStatus) {
            'Approved' => 'paid',
            'WaitingAuthComplete' => 'hold',
            'InProcessing' => 'processing',
            'Declined', 'Expired' => 'failed',
            'Refunded' => 'refunded',
            'Voided' => 'cancelled',
            default => 'pending',
        };
    }
}
