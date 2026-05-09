<?php

namespace App\PipelineSteps\Checkout;

use App\DTO\Checkout\CheckoutContext;
use Closure;
use Linecore\Shoppingcart\Facades\Cart;

final class ValidateCartStep
{
    public function handle(CheckoutContext $context, Closure $next): CheckoutContext
    {
        $items = Cart::instance('shopping')->content();

        if ($items->isEmpty()) {
            $context->fail(__t('Кошик порожній'));

            return $context;
        }

        $context->cartItems = $items;
        $context->subtotal = (float) Cart::instance('shopping')->subtotal(2, '.', '');

        return $next($context);
    }
}
