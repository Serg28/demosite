<?php

namespace App\DTO\Checkout;

readonly class TaxBreakdown
{
    public float $total;

    public function __construct(
        public float $productTax,
        public float $paymentCommission,
    ) {
        $this->total = $productTax + $paymentCommission;
    }

    public static function empty(): self
    {
        return new self(productTax: 0.0, paymentCommission: 0.0);
    }
}
