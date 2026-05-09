<?php

namespace App\Contracts;

interface Installmentable
{
    /**
     * @return array<int, array{months: int, monthly: float, total: float}>
     */
    public function getInstallments(float $amount): array;

    public function getCommission(float $amount, int $months): float;
}
