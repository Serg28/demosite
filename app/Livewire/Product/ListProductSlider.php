<?php

namespace App\Livewire\Product;

use Livewire\Component;

class ListProductSlider extends Component
{
    public function render()
    {
        return view('livewire.product.list-product-slider');
    }

    public function rendered(){
        $this->dispatch('product-list-product-slider-initialized');
    }
}
