<?php

namespace App\Contracts;

interface DiscountSource
{
    public function getType(): string;   // 'promo_code' | 'discount_card'

    public function getAmount(float $subtotal): float;

    public function getLabel(): string;

    public function isCompatibleWith(DiscountSource $other): bool;
}
