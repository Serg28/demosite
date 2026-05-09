<?php

namespace App\Gateways;

use App\Contracts\Holdable;
use App\Contracts\Installmentable;
use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use App\Services\Payment\PrivatPPCommissionStrategy;

class PrivatPayPartsGateway implements PaymentGatewayInterface, Holdable, Installmentable
{
    public function __construct(
        private readonly CredentialResolver $credentials,
        private readonly PrivatPPCommissionStrategy $commission,
    ) {}

    public function init(Order $order): string|array
    {
        $creds = $this->credentials->resolve($order->pay_method_id);
        // TODO: PrivatPay Оплата Частинами init
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

    public function captureHold(Order $order): bool
    {
        return false;
    }

    public function releaseHold(Order $order): bool
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
        }, [2, 4, 6, 10]);
    }

    public function getCommission(float $amount, int $months): float
    {
        return $this->commission->calculate($amount, ['months' => $months]);
    }
}
