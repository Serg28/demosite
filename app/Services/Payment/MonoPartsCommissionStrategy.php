<?php

namespace App\Services\Payment;

use App\Contracts\CommissionStrategy;

class MonoPartsCommissionStrategy implements CommissionStrategy
{
    /**
     * Ставки комісії MonoPay частинами (місяць → %).
     * Актуальні ставки оновлювати тут або виносити в БД.
     *
     * @var array<int, float>
     */
    private const RATES = [
        2  => 1.49,
        4  => 2.49,
        6  => 3.49,
        9  => 4.49,
        12 => 6.49,
        18 => 8.99,
        24 => 11.99,
    ];

    public function calculate(float $amount, array $params = []): float
    {
        $months = (int) ($params['months'] ?? 2);
        $rate = self::RATES[$months] ?? self::RATES[2];

        return round($amount * $rate / 100, 2);
    }
}
