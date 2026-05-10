<?php

namespace App\Services\Payment\Gateways\Privat;

use App\Contracts\Holdable;
use App\Contracts\Installmentable;
use App\Contracts\PaymentGatewayInterface;
use App\Contracts\Returnable;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use App\Services\Payment\Strategies\PrivatPPCommissionStrategy;
use Illuminate\Support\Facades\Log;

class PrivatPayPartsGateway implements Holdable, Installmentable, PaymentGatewayInterface, Returnable
{
    /** Доступні строки розстрочки для PrivatBank PP (місяці). */
    private const AVAILABLE_MONTHS = [2, 4, 6, 10];

    public function __construct(
        private readonly CredentialResolver $credentials,
        private readonly PrivatPPCommissionStrategy $commission,
    ) {}

    /**
     * Ініціалізує платіж "Оплата Частинами" / "Миттєва розстрочка" через PrivatBank.
     *
     * orderId формується як "{order->id}_{time()}" для унікальності при повторних спробах.
     * merchantType (PP/II) береться з credentials['merchant_type'], за замовчуванням PP.
     * partsCount береться з $order->installment_months (0 = покупець обирає на сторінці банку).
     * products — реальний список товарів замовлення; синтетичний запасний якщо список порожній.
     *
     * @return array{payment_url: string, token: string, privat_order_id: string}
     */
    public function init(Order $order): array
    {
        $order->loadMissing(['payMethod', 'products']);

        $creds        = $this->credentials->resolve($order->pay_method_id);
        $client       = $this->makeClientFromCreds($creds);
        $useHold      = $order->payMethod?->use_hold ?? false;
        $merchantType = $creds['merchant_type'] ?? 'PP';
        $partsCount   = (int) ($order->installment_months ?? 0);
        $privatOrderId = $order->id . '_' . time();

        $products = $order->products
            ->map(fn ($p) => [
                'name'  => $p->title ?? __t('Товар'),
                'price' => (float) $p->price,
                'count' => (int) $p->count,
            ])
            ->toArray();

        if (empty($products)) {
            $products = [[
                'name'  => __t('Замовлення № ') . $order->id,
                'count' => 1,
                'price' => $order->getTotalCost() ?: (float) $order->cost,
            ]];
        }

        $result = $client->createPayment(
            orderId:      $privatOrderId,
            amount:       $order->getTotalCost() ?: (float) $order->cost,
            redirectUrl:  route('checkout.success', $order),
            responseUrl:  url('/payment/webhook/privatpayparts'),
            partsCount:   $partsCount,
            products:     $products,
            merchantType: $merchantType,
            useHold:      $useHold,
        );

        return [
            'payment_url'     => $result['paymentUrl'] ?? '',
            'token'           => $result['token'] ?? '',
            'privat_order_id' => $privatOrderId,
        ];
    }

    /**
     * Перевіряє поточний статус через PrivatBank API.
     * Використовує privat_order_id з gateway_response інвойсу.
     */
    public function status(PaymentInvoice $invoice): string
    {
        $invoice->loadMissing('order');
        $client        = $this->makeClient($invoice->order->pay_method_id);
        $privatOrderId = $invoice->gateway_response['privat_order_id'] ?? (string) $invoice->order_id;
        $response      = $client->checkStatus($privatOrderId);

        return $this->mapStatus($response?->paymentState ?? '');
    }

    /**
     * Підтверджує webhook від PrivatBank: перевіряє SHA1-підпис, повертає true для SUCCESS.
     * orderId у payload може бути "{id}_{time}" — (int) парсить числовий префікс для пошуку замовлення.
     *
     * @param  array<string, mixed>  $payload
     */
    public function confirm(array $payload): bool
    {
        $orderId = $payload['orderId'] ?? null;

        if (! $orderId) {
            return false;
        }

        $order = Order::find((int) $orderId);

        if (! $order) {
            return false;
        }

        $client = $this->makeClient($order->pay_method_id);

        if (! $client->verifyWebhookSignature($payload)) {
            Log::channel('payments')->warning('PrivatPayParts: invalid webhook signature', [
                'orderId' => $orderId,
            ]);

            return false;
        }

        return ($payload['paymentState'] ?? '') === 'SUCCESS';
    }

    /**
     * Підтверджує захоплення: переводить LOCKED → SUCCESS.
     */
    public function captureHold(Order $order): bool
    {
        return $this->makeClient($order->pay_method_id)
            ->confirm($this->resolvePrivatOrderId($order));
    }

    /**
     * Скасовує платіж (MERCHANT_CANCEL).
     */
    public function releaseHold(Order $order): bool
    {
        return $this->makeClient($order->pay_method_id)
            ->cancel($this->resolvePrivatOrderId($order));
    }

    /**
     * Повертає кошти після успішного списання (POST /payment/decline).
     * Сума береться з gateway_response['amount'] інвойсу або getTotalCost().
     */
    public function return(Order $order): bool
    {
        $invoice       = $order->paymentInvoices()->latest()->first();
        $privatOrderId = $this->resolvePrivatOrderId($order);
        $invoiceAmount = $invoice?->gateway_response['amount'] ?? null;
        $amount        = (float) ($invoiceAmount ?? ($order->getTotalCost() ?: (float) $order->cost));

        if ($amount <= 0) {
            return false;
        }

        return $this->makeClient($order->pay_method_id)->decline($privatOrderId, $amount);
    }

    /**
     * @return array<int, array{months: int, monthly: float, total: float}>
     */
    public function getInstallments(float $amount): array
    {
        return array_map(function (int $months) use ($amount): array {
            $commission = $this->commission->calculate($amount, ['months' => $months]);
            $total = $amount + $commission;

            return [
                'months'  => $months,
                'monthly' => round($total / $months, 2),
                'total'   => $total,
            ];
        }, self::AVAILABLE_MONTHS);
    }

    public function getCommission(float $amount, int $months): float
    {
        return $this->commission->calculate($amount, ['months' => $months]);
    }

    /**
     * Повертає privat_order_id з останнього інвойсу або fallback на {order->id}.
     * privat_order_id зберігається в gateway_response як "{id}_{time}".
     */
    private function resolvePrivatOrderId(Order $order): string
    {
        $invoice = $order->paymentInvoices()->latest()->first();

        if ($invoice && isset($invoice->gateway_response['privat_order_id'])) {
            return $invoice->gateway_response['privat_order_id'];
        }

        return (string) $order->id;
    }

    private function makeClient(int $payMethodId): PrivatPayPartsClient
    {
        return $this->makeClientFromCreds($this->credentials->resolve($payMethodId));
    }

    /** @param  array<string, mixed>  $creds */
    private function makeClientFromCreds(array $creds): PrivatPayPartsClient
    {
        return new PrivatPayPartsClient(
            storeId:  $creds['merchant_id'] ?? '',
            password: $creds['password'] ?? '',
        );
    }

    private function mapStatus(string $state): string
    {
        return match ($state) {
            'SUCCESS'                                                  => 'paid',
            'LOCKED', 'WAIT_CONFIRM'                                   => 'hold',
            'PROCESSING', 'CLIENT_WAIT'                                => 'processing',
            'RETURNED'                                                 => 'refunded',
            'FAIL', 'TIMEOUT', 'OTP_LIMIT', 'CLIENT_CANCEL',
            'MERCHANT_CANCEL', 'LOOKUP_TIMEOUT', 'PRECHECK_FAIL'      => 'failed',
            default                                                    => 'pending',
        };
    }
}
