<?php

namespace App\Livewire\Product;

use Livewire\Component;

class CounterQty extends Component
{
    public int $quantity = 1;
    public int $min = 1;
    public int $max = 9999;

    public function mount(int $max = 9999): void
    {
        $this->max = max(1, $max);
    }

    public function increment(): void
    {
        if ($this->quantity < $this->max) {
            $this->quantity++;
        }

        $this->dispatch('product-qty-changed', quantity: $this->quantity);
    }

    public function decrement(): void
    {
        if ($this->quantity > $this->min) {
            $this->quantity--;
        }

        $this->dispatch('product-qty-changed', quantity: $this->quantity);
    }

    public function updatedQuantity(): void
    {
        $this->quantity = max($this->min, min($this->max, (int) $this->quantity));

        $this->dispatch('product-qty-changed', quantity: $this->quantity);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.product.counter-qty');
    }
}
