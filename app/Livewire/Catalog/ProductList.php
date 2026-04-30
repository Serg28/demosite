<?php

namespace App\Livewire\Catalog;

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
    public Category $category;

    #[Locked]
    public string $basePath = '';

    public int $page = 1;

    public int $perPage = 24;

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
        $this->basePath = rtrim((string) parse_url($category->getUrl(), PHP_URL_PATH), '/');
        $this->sortBy   = $sortBy;
        $this->sortDir  = $sortDir;
        $this->page     = max(1, $initialPage);

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

    #[On('page-changed')]
    public function setPage(int $page): void
    {
        $this->page = max(1, $page);
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

        return (new LengthAwarePaginator(
            items: $result['products'] ?? [],
            total: $result['total'] ?? 0,
            perPage: $this->perPage,
            currentPage: $this->page,
        ))
            ->withPath($this->basePath)
            ->appends(request()->except('page'));
    }

    public function render(): View
    {
        return view('livewire.catalog.product-list');
    }
}
