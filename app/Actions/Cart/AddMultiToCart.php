<?php

namespace App\Actions\Cart;

use App\DTO\CartResult;
use App\Models\Product;
use App\Services\Cart\CartService;
use Illuminate\Support\Collection;

class AddMultiToCart
{
    public function __construct(private readonly CartService $cartService) {}

    /**
     * Масове додавання товарів у кошик.
     *
     * @param  Collection<int, array{product_id: int, qty: int, options?: array}>  $items
     */
    public function handle(Collection $items): CartResult
    {
        $addedProducts = [];
        $failedCount = 0;

        foreach ($items as $entry) {
            $product = Product::query()->find($entry['product_id'] ?? null);

            if (! $product) {
                $failedCount++;

                continue;
            }

            $context = $this->cartService->addThroughPipeline(
                $product,
                (int) ($entry['qty'] ?? 1),
                (array) ($entry['options'] ?? []),
            );

            if ($context->isFailed()) {
                $failedCount++;

                continue;
            }

            $addedProducts[] = array_merge(
                $product->getProductDataAnalitic(),
                ['quantity' => $context->getResolvedQty()],
            );
        }

        if (empty($addedProducts)) {
            return CartResult::error(__t('Жоден товар не вдалося додати до кошика'));
        }

        return CartResult::success(
            __t('Товари додано до кошика'),
            $this->cartService->count(),
            'add',
            $addedProducts,
        );
    }
}
