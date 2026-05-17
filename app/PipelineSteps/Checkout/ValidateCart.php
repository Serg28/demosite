<?php

namespace App\PipelineSteps\Checkout;

use App\DTO\Checkout\CheckoutContext;
use Closure;
use Linecore\Shoppingcart\Facades\Cart;

final class ValidateCart
{
    public function handle(CheckoutContext $context, Closure $next): CheckoutContext
    {
        $cartInstance = config('cart.checkout_instance', 'default');
        $items = Cart::instance($cartInstance)->content();

        if ($items->isEmpty()) {
            $context->fail(__t('Кошик порожній'));

            return $context;
        }

        $context->cartItems = $items;
        $context->subtotal = (float) Cart::instance($cartInstance)->subtotal(
            config('cart.format.decimals', 2),
            config('cart.format.decimal_point', '.'),
            config('cart.format.thousand_separator', '')
        );

        return $next($context);
    }
}
