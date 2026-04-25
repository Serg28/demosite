<?php

namespace App\Services\Shoppingcart\Calculators;

use Gloudemans\Shoppingcart\CartItem;
use Gloudemans\Shoppingcart\Contracts\Calculator;

/**
 * Класс BasketCalculator
 *
 * Этот класс отвечает за расчет различных атрибутов, связанных с товарами корзины покупок.
 * Он реализует интерфейс Calculator, определенный пакетом корзины покупок, чтобы предоставлять
 * настраиваемые вычисления для элементов корзины.
 *
 * Этот калькулятор позволяет рассчитывать атрибуты, такие как скидки, налоги, общие цены
 * и многое другое для элементов корзины.
 *
 * Использование:
 * Для использования этого калькулятора вам следует указать его в файле конфигурации корзины покупок.
 * Установите ключ 'calculator' в вашем файле 'config/cart.php' в значение 'BasketCalculator'. Это
 * сделает вашу корзину покупок использовать этот калькулятор для выполнения расчетов.
 *
 * Пример конфигурации в 'config/cart.php':
 * ```
 * 'calculator' => \App\Services\Shoppingcart\Calculators\BasketCalculator::class,
 * ```
 *
 * @package App\Services\Shoppingcart\Calculators
 */

class BasketCalculator implements Calculator
{
    /**
     * Рассчитать указанный атрибут для элемента корзины.
     *
     * @param string $attribute
     * @param CartItem $cartItem
     * @return mixed
     */
    public static function getAttribute(string $attribute, CartItem $cartItem): mixed
    {
        $decimals = config('cart.format.decimals', 2);

        switch ($attribute) {

            case 'discount': //Размер скидки на одну единицу товара
                return $cartItem->discountAmount / $cartItem->qty;

            case 'baseAmount': //Базовая сумма одной строки (цена * кол-во) - base_amount
            case 'priceTotal':
                return $cartItem->price*$cartItem->qty;

            case 'discountAmount': //Сумма скидки для всего количества товара в одной строке - discount_amount
            case 'discountTotal':
                $result = $cartItem->baseAmount * ($cartItem->getDiscountRate() / 100);
                $roundedResult = number_format($result, 2, '.', '');
                return ceil($roundedResult);
            case 'totalAmount': //total_amount
                return $cartItem->baseAmount - $cartItem->discountAmount;
            case 'priceTarget': //price
                return $cartItem->totalAmount / $cartItem->qty;
            case 'subtotal':
                return max($cartItem->totalAmount, 0);
            case 'total':
                return $cartItem->subtotal + $cartItem->taxTotal;
            case 'tax':
                return round($cartItem->priceTarget * ($cartItem->taxRate / 100), $decimals);
            case 'priceTax':
                return round($cartItem->priceTarget + $cartItem->tax, $decimals);
            case 'taxTotal':
                return round($cartItem->subtotal * ($cartItem->taxRate / 100), $decimals);
            default:
                return null;
        }
    }
}
