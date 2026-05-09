<?php

namespace App\Services\Cart\Pipes;

use App\Services\Analytics;
use App\Services\Cart\CartContext;
use App\Services\Cart\Contracts\CartPipeInterface;
use Closure;
use Linecore\Shoppingcart\Facades\Cart;

final class PerformAddPipe implements CartPipeInterface
{
    public function handle(CartContext $context, Closure $next): CartContext
    {
        (new Analytics())->setListName($context->product->id);

        $item = Cart::instance($context->cartInstance)->add(
            $context->product->id,
            $context->product->t('title'),
            $context->getResolvedQty(),
            $context->product->getPrice(),
            $context->product->getWeight(),
            $context->options,
        )->associate($context->product);

        $context->setCartItem($item);

        return $next($context);
    }
}
