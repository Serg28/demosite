<?php

namespace App\Services\Payment;

use App\Contracts\CommissionStrategy;

class PrivatPPCommissionStrategy implements CommissionStrategy
{
    /**
     * Ставки комісії PrivatBank Оплата Частинами (місяць → %).
     *
     * @var array<int, float>
     */
    private const RATES = [
        2  => 1.49,
        4  => 2.49,
        6  => 3.49,
        10 => 4.99,
    ];

    public function calculate(float $amount, array $params = []): float
    {
        $months = (int) ($params['months'] ?? 2);
        $rate = self::RATES[$months] ?? self::RATES[2];

        return round($amount * $rate / 100, 2);
    }
}
