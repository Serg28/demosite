<?php

namespace App\Actions\Cart;

use App\DTO\CartResult;
use App\Events\Cart\CartItemRemoved;
use App\Models\Product;
use App\Services\Cart\CartService;
use Linecore\Shoppingcart\Exceptions\InvalidRowIDException;

class RemoveFromCartAction
{
    public function __construct(private readonly CartService $cartService) {}

    public function handle(string $rowId): CartResult
    {
        try {
            $item = $this->cartService->get($rowId);
        } catch (InvalidRowIDException) {
            return CartResult::error(__t('Товар не знайдено у кошику'));
        }

        $qty = $item->qty;
        $this->cartService->remove($rowId);

        $product = Product::query()->find($item->id);
        if ($product) {
            CartItemRemoved::dispatch($product, $qty, $this->cartService->getInstance());
        }

        $analytic = $product
            ? array_merge($product->getProductDataAnalitic(), ['quantity' => $qty])
            : ['item_id' => (string) $item->id, 'item_name' => $item->name, 'price' => (float) $item->price, 'quantity' => $qty];

        return CartResult::success(
            __t('Товар видалено з кошика'),
            $this->cartService->count(),
            'remove',
            [$analytic],
        );
    }
}
