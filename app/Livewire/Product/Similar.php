<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Component;

class Similar extends Component
{
    public int $productId;
    public int $limit = 8;

    /** @var Collection<int, Product> */
    private Collection $list;

    public function mount(Product $product, int $limit = 8): void
    {
        $this->productId = $product->id;
        $this->limit = $limit;

        $list = $product->interestingProducts()
            ->active()
            ->limit($this->limit)
            ->get();

        if ($list->isEmpty() && $product->category_id) {
            $list = Product::query()
                ->active()
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->limit($this->limit)
                ->get(['id', 'title', 'price', 'price_old', 'picture', 'slug', 'is_active', 'quantity']);
        }

        $this->list = $list;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.product.similar', ['list' => $this->list]);
    }
}
