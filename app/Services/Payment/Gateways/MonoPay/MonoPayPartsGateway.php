<?php

namespace App\Services\Payment\Gateways\MonoPay;

use App\Contracts\Holdable;
use App\Contracts\Installmentable;
use App\Contracts\PaymentGatewayInterface;
use App\Contracts\Returnable;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use App\Services\Payment\Strategies\MonoPartsCommissionStrategy;
use Illuminate\Support\Facades\Log;

class MonoPayPartsGateway implements Holdable, Installmentable, PaymentGatewayInterface, Returnable
{
    /** @var int[] Доступні строки розстрочки (місяці) за замовчуванням. */
    private const AVAILABLE_MONTHS = [2, 4, 6, 9, 12, 18, 24];

    public function __construct(
        private readonly CredentialResolver $credentials,
        private readonly MonoPartsCommissionStrategy $commission,
    ) {}

    /**
     * Ініціалізує заявку MonoPayParts через MonoBank Parts API.
     * Покупець отримує push-повідомлення у додатку монобанк.
     *
     * @return array{mono_order_id: string, store_order_id: string, url: string}
     */
    public function init(Order $order): array
    {
        $order->loadMissing(['payMethod', 'products']);
        $client = $this->makeClient($order->pay_method_id);

        $storeOrderId = $order->id . '_' . time();
        $months       = (int) ($order->installment_months ?? 0);
        $partsCount   = $months > 0 ? [$months] : self::AVAILABLE_MONTHS;

        $body = [
            'store_order_id'     => $storeOrderId,
            'client_phone'       => $order->phone ?? '',
            'total_sum'          => round($order->getTotalCost(), 2),
            'invoice'            => [
                'date'   => now()->format('Y-m-d'),
                'number' => (string) $order->id,
                'source' => 'INTERNET',
            ],
            'available_programs' => [
                [
                    'available_parts_count' => $partsCount,
                    'type'                  => 'payment_installments',
                ],
            ],
            'products'           => $this->buildProducts($order),
            'result_callback'    => url('/payment/webhook/monopayparts'),
        ];

        $response = $client->create($body) ?? [];

        if (isset($response['errors'])) {
            Log::channel('payments')->error('MonoParts: order create failed', [
                'order_id' => $order->id,
                'errors'   => $response['errors'],
            ]);
        }

        return [
            'mono_order_id'  => $response['order_id'] ?? '',
            'store_order_id' => $storeOrderId,
            'url'            => route('checkout.success', $order),
        ];
    }

    /**
     * Перевіряє поточний статус заявки через MonoBank Parts API.
     */
    public function status(PaymentInvoice $invoice): string
    {
        $monoOrderId = $invoice->gateway_response['mono_order_id'] ?? null;

        if (! $monoOrderId) {
            return 'pending';
        }

        $invoice->loadMissing('order');
        $client   = $this->makeClient($invoice->order->pay_method_id);
        $response = $client->state($monoOrderId) ?? [];

        return $this->mapStatus(
            $response['state'] ?? '',
            $response['order_sub_state'] ?? ''
        );
    }

    /**
     * Підтверджує webhook від MonoBank Parts: перевіряє підпис та стан заявки.
     * WAITING_FOR_STORE_CONFIRM = кредит підтверджено клієнтом, можна передавати товар.
     *
     * @param  array<string, mixed>  $payload
     */
    public function confirm(array $payload): bool
    {
        $rawBody = $payload['_raw_body'] ?? null;

        if ($rawBody !== null) {
            $signature = $payload['_signature'] ?? '';
            $orderId   = $payload['order_id'] ?? null;

            if ($orderId) {
                $invoice = PaymentInvoice::where('gateway_response->mono_order_id', $orderId)->first();

                if ($invoice) {
                    $invoice->loadMissing('order');
                    $client = $this->makeClient($invoice->order->pay_method_id);

                    if (! $client->verifyWebhookSignature($rawBody, $signature)) {
                        Log::channel('payments')->warning('MonoParts: invalid webhook signature', [
                            'mono_order_id' => $orderId,
                        ]);

                        return false;
                    }
                }
            }
        }

        $state    = $payload['state'] ?? '';
        $subState = $payload['order_sub_state'] ?? '';

        if ($state === 'IN_PROCESS' && $subState === 'WAITING_FOR_STORE_CONFIRM') {
            return true;
        }

        return $state === 'SUCCESS'
            && in_array($subState, ['ACTIVE', 'DONE', 'EARLY_CLOSED_BY_CLIENT'], true);
    }

    /**
     * Підтверджує видачу товару клієнтові — WAITING_FOR_STORE_CONFIRM → confirm.
     */
    public function captureHold(Order $order): bool
    {
        $monoOrderId = $this->resolveMonoOrderId($order);

        if (! $monoOrderId) {
            return false;
        }

        $client   = $this->makeClient($order->pay_method_id);
        $response = $client->confirm($monoOrderId) ?? [];

        return isset($response['state']) && ! isset($response['error']);
    }

    /**
     * Скасовує заявку (товар не видано клієнтові).
     */
    public function releaseHold(Order $order): bool
    {
        $monoOrderId = $this->resolveMonoOrderId($order);

        if (! $monoOrderId) {
            return false;
        }

        $client   = $this->makeClient($order->pay_method_id);
        $response = $client->reject($monoOrderId) ?? [];

        return isset($response['state']) && ! isset($response['error']);
    }

    /**
     * Повертає кошти клієнтові (повне повернення).
     */
    public function return(Order $order): bool
    {
        $monoOrderId = $this->resolveMonoOrderId($order);

        if (! $monoOrderId) {
            return false;
        }

        $storeReturnId = $order->id . '_ret_' . time();
        $client        = $this->makeClient($order->pay_method_id);
        $response      = $client->returnOrder($monoOrderId, $storeReturnId, $order->getTotalCost()) ?? [];

        return isset($response['state']) && ! isset($response['error']);
    }

    /**
     * @return array<int, array{months: int, monthly: float, total: float}>
     */
    public function getInstallments(float $amount): array
    {
        return array_map(function (int $months) use ($amount): array {
            $commission = $this->commission->calculate($amount, ['months' => $months]);
            $total      = $amount + $commission;

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

    private function buildProducts(Order $order): array
    {
        if ($order->products->isNotEmpty()) {
            return $order->products->map(fn ($p) => [
                'name'  => $this->sanitizeProductName($p->title ?? __t('Товар')),
                'count' => (int) $p->count,
                'sum'   => round((float) $p->price, 2),
            ])->toArray();
        }

        return [
            [
                'name'  => __t('Замовлення № ') . $order->id,
                'count' => 1,
                'sum'   => round($order->getTotalCost(), 2),
            ],
        ];
    }

    private function sanitizeProductName(string $name): string
    {
        return str_replace(["'", '"', '&#39;', '&'], '', htmlspecialchars_decode($name));
    }

    private function makeClient(int $payMethodId): MonoPartsClient
    {
        $creds = $this->credentials->resolve($payMethodId);

        return new MonoPartsClient(
            storeId: $creds['store_id'] ?? '',
            secret:  $creds['secret'] ?? '',
            apiUrl:  $creds['api_url'] ?? config('services.monopayparts.api_base_url', 'https://u2-demo-ext.mono.st4g3.com'),
        );
    }

    private function resolveMonoOrderId(Order $order): ?string
    {
        return $order->paymentInvoices()
            ->latest()
            ->first()
            ?->gateway_response['mono_order_id'] ?? null;
    }

    private function mapStatus(string $state, string $subState): string
    {
        if ($state === 'SUCCESS') {
            return match ($subState) {
                'RETURNED'                                    => 'refunded',
                'ACTIVE', 'DONE', 'EARLY_CLOSED_BY_CLIENT'   => 'paid',
                default                                       => 'paid',
            };
        }

        if ($state === 'IN_PROCESS') {
            return $subState === 'WAITING_FOR_STORE_CONFIRM' ? 'hold' : 'processing';
        }

        if ($state === 'FAIL') {
            return 'failed';
        }

        return 'pending';
    }
}
