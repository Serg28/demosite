<?php

namespace App\Actions\Cart;

use App\DTO\CartResult;
use App\Events\Cart\CartItemUpdated;
use App\Models\Product;
use App\Services\Cart\CartService;
use Linecore\Shoppingcart\Exceptions\InvalidRowIDException;

class UpdateCartItem
{
    public function __construct(private readonly CartService $cartService) {}

    public function handle(string $rowId, int $qty): CartResult
    {
        try {
            $item = $this->cartService->get($rowId);
        } catch (InvalidRowIDException) {
            return CartResult::error(__t('Товар не знайдено у кошику'));
        }

        $product = Product::query()->find($item->id);
        $qtyBefore = $item->qty;
        $available = $product
            ? $this->cartService->availableForOrder($product, $qty, 'update')
            : max(1, $qty);

        $this->cartService->update($rowId, $available);

        if ($product) {
            CartItemUpdated::dispatch($product, $qtyBefore, $available, $this->cartService->getInstance());
        }

        // GA4 best practice: delta qty з правильним event (add_to_cart або remove_from_cart)
        $delta = abs($available - $qtyBefore);
        $ga4Action = $available >= $qtyBefore ? 'add' : 'remove';

        $analytic = $product
            ? array_merge($product->getProductDataAnalitic(), ['quantity' => $delta])
            : ['item_id' => (string) $item->id, 'item_name' => $item->name, 'price' => (float) $item->price, 'quantity' => $delta];

        return CartResult::success(
            __t('Кількість оновлено'),
            $this->cartService->count(),
            $ga4Action,
            [$analytic],
        );
    }
}
