<?php

namespace App\Cart;

use App\Models\Product;
use Linecore\Shoppingcart\CartItem;

/**
 * Контекст операції з кошиком — єдиний payload для Cart Pipeline.
 * Кожен Pipe може змінювати стан через setters.
 */
final class CartContext
{
    private bool $failed = false;

    private string $failureReason = '';

    private ?CartItem $cartItem = null;

    private int $resolvedQty;

    public function __construct(
        public readonly Product $product,
        public readonly int $requestedQty,
        public readonly string $cartInstance = 'default',
        public readonly array $options = [],
    ) {
        $this->resolvedQty = $requestedQty;
    }

    public function fail(string $reason): void
    {
        $this->failed = true;
        $this->failureReason = $reason;
    }

    public function setResolvedQty(int $qty): void
    {
        $this->resolvedQty = $qty;
    }

    public function setCartItem(CartItem $item): void
    {
        $this->cartItem = $item;
    }

    public function isFailed(): bool
    {
        return $this->failed;
    }

    public function getFailureReason(): string
    {
        return $this->failureReason;
    }

    public function getResolvedQty(): int
    {
        return $this->resolvedQty;
    }

    public function getCartItem(): ?CartItem
    {
        return $this->cartItem;
    }
}
