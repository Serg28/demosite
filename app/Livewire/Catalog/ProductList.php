<?php

namespace App\Livewire\Catalog;

use App\Livewire\Concerns\HasPagination;
use App\Models\Category;
use App\Services\TypeSenseService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductList extends Component
{
    use HasPagination;

    public Category $category;

    #[Locked]
    public string $sortBy = 'priority';

    #[Locked]
    public string $sortDir = 'desc';

    #[Locked]
    public array $filters = [];

    public function mount(
        Category $category,
        string $sortBy = 'priority',
        string $sortDir = 'desc',
        array $initialFilters = [],
        int $initialPage = 1,
    ): void {
        $this->category = $category;
        $this->bootPagination($category->getUrl());
        $this->sortBy = $sortBy;
        $this->sortDir = $sortDir;
        $this->page = max(1, $initialPage);

        if (! empty($initialFilters)) {
            $this->filters = $initialFilters;
        }
    }

    #[On('filtersUpdated')]
    public function applyFilters(array $filters, int $page = 1): void
    {
        $this->filters = $filters;
        $this->page    = max(1, $page);
        $this->dispatch('product-list-reset');
    }

    #[On('sort-changed')]
    public function onSortChanged(string $sortKey = ''): void
    {
        [$this->sortBy, $this->sortDir] = $this->resolveSortKey($sortKey);
        $this->page = 1;
        $this->dispatch('product-list-reset');
    }

    /** @return array{string, string} */
    private function resolveSortKey(string $sortKey): array
    {
        foreach (config('catalog.sort_options') as $option) {
            if ($option['url_key'] === ($sortKey ?: null)) {
                return [$option['key'], $option['dir']];
            }
        }

        $default = config('catalog.sort_options.0');

        return [$default['key'], $default['dir']];
    }

    #[Computed]
    public function products(): LengthAwarePaginator
    {
        $result = app(TypeSenseService::class)->search(
            query: '',
            filters: [
                'category_id' => $this->category->id,
                ...$this->filters,
            ],
            page: $this->page,
            pageSize: $this->perPage,
            sortBy: $this->sortBy,
            sortDir: $this->sortDir
        );

        return $this->makePaginator(
            items: $result['products'] ?? [],
            total: $result['total'] ?? 0,
        );
    }

    public function render(): View
    {
        $this->dispatch('catalog-count-updated', count: $this->products->total());

        return view('livewire.catalog.product-list');
    }
}
