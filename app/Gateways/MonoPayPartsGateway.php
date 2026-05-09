<?php

namespace App\Gateways;

use App\Contracts\Installmentable;
use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use App\Services\Payment\MonoPartsCommissionStrategy;

class MonoPayPartsGateway implements PaymentGatewayInterface, Installmentable
{
    public function __construct(
        private readonly CredentialResolver $credentials,
        private readonly MonoPartsCommissionStrategy $commission,
    ) {}

    public function init(Order $order): string|array
    {
        $creds = $this->credentials->resolve($order->pay_method_id);
        // TODO: MonoPay частинами init
        return '';
    }

    public function status(PaymentInvoice $invoice): string
    {
        return 'pending';
    }

    public function confirm(array $payload): bool
    {
        return false;
    }

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
        }, [2, 4, 6, 9, 12, 18, 24]);
    }

    public function getCommission(float $amount, int $months): float
    {
        return $this->commission->calculate($amount, ['months' => $months]);
    }
}
