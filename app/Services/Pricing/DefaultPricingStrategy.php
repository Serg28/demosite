<?php

namespace App\Services\Pricing;

use App\Contracts\PricingStrategy;
use App\Models\Product;

final class DefaultPricingStrategy implements PricingStrategy
{
    public function getPrice(Product $product): float
    {
        return $product->getPrice();
    }

    public function getOldPrice(Product $product): ?float
    {
        return $product->getPriceOld();
    }
}
