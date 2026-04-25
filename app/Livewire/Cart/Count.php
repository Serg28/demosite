<?php

namespace App\Livewire\Cart;

use App\Traits\CartOpener;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Attributes\On;

class Count extends Component
{
    use CartOpener;

    #[On('cart-changed')]
    #[On('cart-product-added')]
    public function loadCart(): void
    {
    }

    public function placeholder(): View
    {
        return view('livewire.cart.count-empty');
    }

    public function render(): View
    {
        $cart = [
            'cartCount' => Cart::count(),
            'productsInCart' => Cart::content(),
            'cartTotal' => Cart::total(),
            'cartSubTotal' => Cart::subtotal()
        ];

        return view('livewire.cart.count')->with($cart);
    }
}
