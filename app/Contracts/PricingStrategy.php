<?php

namespace App\Contracts;

use App\Models\Product;

interface PricingStrategy
{
    public function getPrice(Product $product): float;

    public function getOldPrice(Product $product): ?float;
}
