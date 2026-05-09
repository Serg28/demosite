<?php

namespace App\Contracts;

interface CommissionStrategy
{
    public function calculate(float $amount, array $params = []): float;
}
