<?php

namespace App\Actions\Cart;

use App\DTO\CartResult;
use App\Events\Cart\CartItemAdded;
use App\Models\Product;
use App\Services\CartService;

class AddToCartAction
{
    public function __construct(private readonly CartService $cartService) {}

    /**
     * @param  list<class-string>  $extraPipes  Додаткові Pipeline pipes для бізнес-логіки
     */
    public function handle(Product $product, int $qty = 1, array $options = [], array $extraPipes = []): CartResult
    {
        $context = $this->cartService->addThroughPipeline($product, $qty, $options, $extraPipes);

        if ($context->isFailed()) {
            return CartResult::error($context->getFailureReason());
        }

        CartItemAdded::dispatch($product, $context->getResolvedQty(), $this->cartService->getInstance());

        return CartResult::success(
            __t('Товар додано до кошика'),
            $this->cartService->count(),
            'add',
            [array_merge($product->getProductDataAnalitic(), ['quantity' => $context->getResolvedQty()])],
        );
    }
}
