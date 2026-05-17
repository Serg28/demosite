<?php

namespace App\PipelineSteps\Checkout;

use App\DTO\Checkout\CheckoutContext;
use App\Models\UnfinishedBasket;
use Closure;
use Linecore\Shoppingcart\Facades\Cart;

final class ClearCart
{
    public function handle(CheckoutContext $context, Closure $next): CheckoutContext
    {
        Cart::instance(config('cart.checkout_instance', 'default'))->destroy();

        if ($context->userId) {
            UnfinishedBasket::where('user_id', $context->userId)->delete();
        }

        return $next($context);
    }
}
