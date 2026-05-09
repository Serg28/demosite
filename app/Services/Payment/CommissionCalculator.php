<?php

namespace App\Services\Payment;

use App\Contracts\CommissionStrategy;
use App\Models\PayMethod;

class CommissionCalculator
{
    public function calculate(PayMethod $payMethod, float $amount): float
    {
        $strategy = $this->resolveStrategy($payMethod->slug);

        return $strategy->calculate($amount, [
            'pay_method' => $payMethod,
        ]);
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
