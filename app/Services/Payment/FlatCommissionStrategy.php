<?php

namespace App\Services\Payment;

use App\Contracts\CommissionStrategy;
use App\Models\PayMethod;

class FlatCommissionStrategy implements CommissionStrategy
{
    public function calculate(float $amount, array $params = []): float
    {
        /** @var PayMethod|null $payMethod */
        $payMethod = $params['pay_method'] ?? null;
        $percent = (float) ($payMethod?->commission_percent ?? 0.0);

        if ($percent === 0.0) {
            return 0.0;
        }

        return round($amount * $percent / 100, 2);
    }
}
