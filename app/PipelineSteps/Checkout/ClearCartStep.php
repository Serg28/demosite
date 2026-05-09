<?php

namespace App\PipelineSteps\Checkout;

use App\DTO\Checkout\CheckoutContext;
use App\Models\UnfinishedBasket;
use Closure;
use Linecore\Shoppingcart\Facades\Cart;

final class ClearCartStep
{
    public function handle(CheckoutContext $context, Closure $next): CheckoutContext
    {
        Cart::instance('shopping')->destroy();

        if ($context->userId) {
            UnfinishedBasket::where('user_id', $context->userId)->delete();
        }

        return $next($context);
    }
}
