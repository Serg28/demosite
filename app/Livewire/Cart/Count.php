<?php

namespace App\Livewire\Cart;

use Illuminate\View\View;
use Linecore\Shoppingcart\Facades\Cart;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy(isolate: false)]
class Count extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->loadCart();
    }

    #[On('cart-changed')]
    public function loadCart(): void
    {
        $this->count = Cart::count();
    }

    public function render(): View
    {
        return view('livewire.cart.count');
    }
}
