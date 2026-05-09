<?php

namespace App\DTO\Checkout;

readonly class DiscountBreakdown
{
    public float $total;

    public function __construct(
        public float $promoCode,
        public float $discountCard,
    ) {
        $this->total = $promoCode + $discountCard;
    }

    public static function empty(): self
    {
        return new self(promoCode: 0.0, discountCard: 0.0);
    }
}
