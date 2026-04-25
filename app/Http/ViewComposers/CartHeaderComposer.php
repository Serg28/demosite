<?php

namespace App\Http\ViewComposers;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\View\View;

class CartHeaderComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('CartHeaderComposer', 'Time for CartHeaderComposer');
        $cartCount = Cart::count();
        $productsInCart = Cart::content();
        $cartTotal = Cart::total();
        $cartSubTotal = Cart::subtotal();

        $view->with(compact('cartCount', 'productsInCart', 'cartTotal', 'cartSubTotal'));
        debugbar()->stopMeasure('CartHeaderComposer');
    }
}
