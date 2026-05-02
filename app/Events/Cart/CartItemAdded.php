<?php

namespace App\Events\Cart;

use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;

final class CartItemAdded
{
    use Dispatchable;

    public function __construct(
        public readonly Product $product,
        public readonly int $qty,
        public readonly string $cartInstance = 'default',
    ) {}
}
