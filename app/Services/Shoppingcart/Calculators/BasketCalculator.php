<?php

namespace App\Services\Shoppingcart\Calculators;

use Linecore\Shoppingcart\CartItem;
use Linecore\Shoppingcart\Contracts\Calculator;

class BasketCalculator implements Calculator
{
    public static function getAttribute(string $attribute, CartItem $cartItem): mixed
    {
        $decimals         = config('cart.format.decimals', 2);
        $decimalPoint     = config('cart.format.decimal_point', '.');
        $thousandSep      = config('cart.format.thousand_separator', '');

        return match ($attribute) {
            'discount'                    => $cartItem->discountAmount / max($cartItem->qty, 1),
            'baseAmount', 'priceTotal'    => $cartItem->price * $cartItem->qty,
            'discountAmount', 'discountTotal' => (function () use ($cartItem, $decimals, $decimalPoint, $thousandSep): int {
                $result = $cartItem->baseAmount * ($cartItem->getDiscountRate() / 100);
                return (int) ceil(number_format($result, $decimals, $decimalPoint, $thousandSep));
            })(),
            'totalAmount'                 => $cartItem->baseAmount - $cartItem->discountAmount,
            'priceTarget'                 => $cartItem->totalAmount / max($cartItem->qty, 1),
            'subtotal'                    => max($cartItem->totalAmount, 0),
            'total'                       => $cartItem->subtotal + $cartItem->taxTotal,
            'tax'                         => round($cartItem->priceTarget * ($cartItem->taxRate / 100), $decimals),
            'priceTax'                    => round($cartItem->priceTarget + $cartItem->tax, $decimals),
            'taxTotal'                    => round($cartItem->subtotal * ($cartItem->taxRate / 100), $decimals),
            default                       => null,
        };
    }
}
