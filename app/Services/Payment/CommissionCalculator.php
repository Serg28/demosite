<?php

namespace App\Services\Payment;

use App\Contracts\CommissionStrategy;
use App\Models\PayMethod;
use App\Services\Payment\Strategies\FlatCommissionStrategy;

class CommissionCalculator
{
    public function calculate(PayMethod $payMethod, float $amount): float
    {
        $raw = $this->resolveStrategy($payMethod->slug)->calculate($amount, [
            'pay_method' => $payMethod,
        ]);

        return round($raw, config('cart.format.decimals', 2));
    }

    private function resolveStrategy(string $slug): CommissionStrategy
    {
        $strategies = config('payment.commission_strategies', []);

        $strategyClass = $strategies[$slug] ?? config(
            'payment.default_commission_strategy',
            FlatCommissionStrategy::class,
        );

        return app($strategyClass);
    }
}
