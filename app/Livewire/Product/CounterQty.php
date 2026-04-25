<?php

namespace App\Livewire\Product;

use App\Livewire\Cart\Addtocart;
use Livewire\Component;

class CounterQty extends Component
{
    public int|float $quantity = 1;
    public int|float $min = 1;
    public int|float $max = 1;

    public function decrement(): void
    {
        $this->quantity = max($this->min, $this->quantity - 1);
        $this->updatedQuantity();
    }

    public function increment(): void
    {
        $this->quantity = min($this->max, $this->quantity + 1);
        $this->updatedQuantity();
    }

    // Устанавливает в товаре количество добавляемого товара в корзину
    // Метод автоматически срабатывает при изменении поля quantity.
    public function updatedQuantity(): void
    {
        if (!is_numeric($this->quantity) || $this->quantity < 1) {
            $this->quantity = 1;
        }

        $this->quantity = max($this->min, min($this->max, $this->quantity));

        $this->dispatch('product-input-quantity-set', quantity: $this->quantity)->to(Addtocart::class);
    }

    public function render()
    {
        return view('livewire.product.counter-qty');
    }
}
