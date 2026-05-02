<?php

namespace App\Events\Cart;

use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;

final class CartItemUpdated
{
    use Dispatchable;

    public readonly int $delta;

    public readonly string $direction;

    public function __construct(
        public readonly Product $product,
        public readonly int $qtyBefore,
        public readonly int $qtyAfter,
        public readonly string $cartInstance = 'default',
    ) {
        $this->delta = abs($qtyAfter - $qtyBefore);
        $this->direction = $qtyAfter >= $qtyBefore ? 'increase' : 'decrease';
    }
}
