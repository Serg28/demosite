<?php

namespace App\Livewire\Product;

use Livewire\Component;

class BaseCharacteristics extends Component {

    public array|null $characteristics;

    public function render() {
        return view('livewire.product.base-characteristics');
    }

    public function rendered(){
        $this->dispatch('product-base-characteristics-initialized');
    }

    public function placeholder() {
        return view('livewire.product.base-characteristics-empty');
    }
}