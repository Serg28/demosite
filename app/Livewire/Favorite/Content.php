<?php

namespace App\Livewire\Favorite;

use App\Models\Product;
use App\Services\Sort\FilterSorting;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\LivewireShowMore;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;

class Content extends Component
{

    use WithPagination;
    use LivewireShowMore;

    private $limit = 12;

    private FilterSorting $sortService;

    public function boot()
    {
        $this->sortService = new FilterSorting();
    }

    public function render()
    {
        $user = app('user');

        $query = ($user) ? Product::whereLikedBy($user->id) : collect();

        $query->orderBy($this->sortService->getOrderField(), $this->sortService->getOrderDirect());

        $products = $query->cardFields()->paginate($this->limit);
        $results['products'] = $products;
        $products = $this->handleLivewireShowMore($results);

        if($products->isEmpty()) {
            $this->resetPage(); // Сбрасываем пагинацию, если на текущей странице нету товаров
        }

        return view('livewire.favorite.content', [
            'products' => $products,
            'results' => $results ?? null,
            'filter' => $this->sortService,
            'count' => $products->count(),
        ]);
    }

    #[On('updateFavoriteList')]
    #[On('updateFavoriteCount')]
    public function updateFavoriteList()
    {
        $this->render();
    }
}
