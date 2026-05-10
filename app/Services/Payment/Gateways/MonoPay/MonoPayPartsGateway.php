<?php

namespace App\Services\Payment\Gateways\MonoPay;

use App\Contracts\Installmentable;
use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use App\Services\Payment\Strategies\MonoPartsCommissionStrategy;
use Illuminate\Support\Facades\Log;

class MonoPayPartsGateway implements PaymentGatewayInterface, Installmentable
{
    /** Доступні строки розстрочки (місяці). */
    private const AVAILABLE_MONTHS = [2, 4, 6, 9, 12, 18, 24];

    public function __construct(
        private readonly CredentialResolver $credentials,
        private readonly MonoPartsCommissionStrategy $commission,
    ) {}

    /**
     * Ініціалізує платіж частинами через MonoPay.
     * Строк розстрочки вибирає покупець на сторінці MonoPay.
     *
     * @return array{invoice_id: string, page_url: string}
     */
    public function init(Order $order): array
    {
        $client = $this->makeClient($order->pay_method_id);
        $result = $client->create([
            'amount'           => $this->toKopecks($order->getTotalCost()),
            'ccy'              => 980, // UAH
            'merchantPaymInfo' => [
                'reference'   => (string) $order->id,
                'destination' => __t('Оплата замовлення № ') . $order->id . ' ' . __t('у розстрочку'),
            ],
            'redirectUrl'      => route('checkout.success', $order),
            'webHookUrl'       => url('/payment/webhook/monopayparts'),
            'validity'         => 3600,
            'paymentType'      => 'deferred',
        ]);

        return [
            'invoice_id' => $result['invoiceId'] ?? '',
            'page_url'   => $result['pageUrl'] ?? '',
        ];
    }

    /**
     * Перевіряє поточний статус оплати через MonoPay API.
     */
    public function status(PaymentInvoice $invoice): string
    {
        $invoiceId = $invoice->gateway_response['invoice_id'] ?? null;

        if (! $invoiceId) {
            return 'pending';
        }

        $invoice->loadMissing('order');
        $client   = $this->makeClient($invoice->order->pay_method_id);
        $response = $client->status($invoiceId);

        return $this->mapStatus($response?->status ?? '');
    }

    /**
     * Підтверджує webhook від MonoPay.
     *
     * @param  array{status: string, reference?: string, _raw_body?: string, _x_sign?: string}  $payload
     */
    public function confirm(array $payload): bool
    {
        $status  = $payload['status'] ?? '';
        $rawBody = $payload['_raw_body'] ?? null;

        if ($rawBody !== null) {
            $xSign     = $payload['_x_sign'] ?? '';
            $reference = $payload['reference'] ?? null;
            $order     = $reference ? \App\Models\Order::find((int) $reference) : null;

            if ($order) {
                $client = $this->makeClient($order->pay_method_id);

                if (! $client->verifyWebhookSignature($rawBody, $xSign)) {
                    Log::channel('payments')->warning('MonoPayParts: invalid webhook signature', [
                        'reference' => $reference,
                    ]);

                    return false;
                }
            }
        }

        return $status === 'success';
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

    private function makeClient(int $payMethodId): MonoPayClient
    {
        $creds = $this->credentials->resolve($payMethodId);

        return new MonoPayClient($creds['token'] ?? '');
    }

    private function mapStatus(string $monoStatus): string
    {
        return match ($monoStatus) {
            'success'                        => 'paid',
            'processing'                     => 'processing',
            'failure', 'reversed', 'expired' => 'failed',
            default                          => 'pending',
        };
    }

    private function toKopecks(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
