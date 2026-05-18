<?php

namespace App\Services\Payment\Strategies;

use App\Contracts\CommissionStrategy;

class PrivatPPCommissionStrategy implements CommissionStrategy
{
    /**
     * Ставки комісії PrivatBank Оплата Частинами (місяць → %).
     *
     * @var array<int, float>
     */
    /** @var array<int, float> */
    private const DEFAULT_RATES = [
        2  => 1.49,
        4  => 2.49,
        6  => 3.49,
        10 => 4.99,
    ];

    public function calculate(float $amount, array $params = []): float
    {
        $months = (int) ($params['months'] ?? 2);
        $rates = $params['rates'] ?? self::DEFAULT_RATES;
        $rate = $rates[$months] ?? $rates[array_key_first($rates)] ?? 1.49;

        return round($amount * $rate / 100, config('cart.format.decimals', 2));
    }
}
