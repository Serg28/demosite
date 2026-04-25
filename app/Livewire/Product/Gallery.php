<?php

namespace App\Livewire\Product;

use Livewire\Component;

class Gallery extends Component
{
    private $page;

    private $otherPictures;

    public function mount($page, $otherPictures): void
    {
        $this->page = $page;
        $this->otherPictures = $otherPictures;
    }

    public function rendered() {
        $this->dispatch('product-slider-initialized');
    }

    public function render()
    {
        return view('livewire.product.gallery', [
            'page' => $this->page,
            'otherPictures' => $this->otherPictures
        ]);
    }

}
