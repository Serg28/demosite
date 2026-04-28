<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use App\Services\TypeSenseService;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductList extends Component
{
    public Category $category;

    public int $page = 1;

    public int $morePages = 0;

    public int $perPage = 24;

    #[Locked]
    public array $filters = [];

    #[Locked]
    public string $sortBy = 'priority';

    #[Locked]
    public string $sortDir = 'desc';

    public function mount(Category $category, string $sortBy = 'priority', string $sortDir = 'desc', array $initialFilters = []): void
    {
        $this->category = $category;
        $this->sortBy = $sortBy;
        $this->sortDir = $sortDir;

        if (!empty($initialFilters)) {
            $this->filters = $initialFilters;
        }
    }

    #[On('filtersUpdated')]
    public function applyFilters(array $filters): void
    {
        $this->filters = $filters;
        $this->page = 1;
        $this->morePages = 0;
        $this->dispatch('product-list-reset');
    }

    #[On('sortUpdated')]
    public function applySort(string $sortBy, string $sortDir): void
    {
        $this->sortBy = $sortBy;
        $this->sortDir = $sortDir;
        $this->page = 1;
        $this->morePages = 0;
    }

    public function setPage(int $page): void
    {
        $this->page = max(1, $page);
        $this->morePages = 0;
        $this->dispatch('product-list-reset');
    }

    public function resetPage(): void
    {
        $this->page = 1;
        $this->morePages = 0;
        $this->dispatch('product-list-reset');
    }

    public function loadMore(): void
    {
        $this->morePages++;
    }

    /**
     * Fetches $morePages+1 consecutive TypeSense pages and merges them.
     * Products are never stored in snapshot — recalculated on every render.
     *
     * @return array{products: mixed[], total: int, current_page: int, last_page: int, has_more: bool, per_page: int}
     */
    #[Computed]
    public function products(): array
    {
        $service = app(TypeSenseService::class);
        $allProducts = [];
        $lastResult = ['total' => 0, 'last_page' => 1, 'products' => []];

        for ($i = 0; $i <= $this->morePages; $i++) {
            $lastResult = $service->search(
                query: '',
                filters: [
                    'category_id' => $this->category->id,
                    ...$this->filters,
                ],
                page: $this->page + $i,
                pageSize: $this->perPage,
                sortBy: $this->sortBy,
                sortDir: $this->sortDir
            );
            $allProducts = array_merge($allProducts, $lastResult['products'] ?? []);
        }

        return [
            'products' => $allProducts,
            'total' => $lastResult['total'] ?? 0,
            'current_page' => $this->page,
            'last_page' => $lastResult['last_page'] ?? 1,
            'has_more' => ($this->page + $this->morePages) < ($lastResult['last_page'] ?? 1),
            'per_page' => $this->perPage,
        ];
    }

    public function render(): View
    {
        return view('livewire.catalog.product-list');
    }
}
