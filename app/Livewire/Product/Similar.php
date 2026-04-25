<?php

namespace App\Livewire\Product;

use Livewire\Component;

class Similar extends Component
{
    private $page;
    private $list = [];

    public function mount($page): void
    {
        $this->page = $page;
        $this->list = $page->interestingProducts()->active()
            ->rememberForever()->cacheTags(['products'])
            ->get()->shuffle();
    }

//    public function rendered() {
//        $this->dispatch('product-slider-initialized');
//    }

    public function render()
    {
        return view('livewire.product.similar', [
            'page' => $this->page,
            'list' => $this->list
        ]);
    }

    public function rendered()
    {
        $this->dispatch('product-similar-initialized');
    }

}
