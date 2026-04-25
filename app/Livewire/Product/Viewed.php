<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Livewire\Component;

class Viewed extends Component
{
    private $count = 10;

    public function render()
    {
        return view('livewire.product.viewed.index', ['list' => $this->getView()]);
    }
    public function placeholder()
    {
        return view('livewire.product.viewed.empty');
    }

    private function getView() {
        return (new Product())->getView()?->splice(0, 10);
    }
}
