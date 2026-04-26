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

    #[Locked]
    public array $filters = [];

    #[Locked]
    public string $sortBy = 'priority';

    #[Locked]
    public string $sortDir = 'desc';

    public function mount(Category $category, string $sortBy = 'priority', string $sortDir = 'desc'): void
    {
        $this->category = $category;
        $this->sortBy = $sortBy;
        $this->sortDir = $sortDir;
    }

    #[On('filtersUpdated')]
    public function applyFilters(array $filters): void
    {
        $this->filters = $filters;
        $this->page = 1;
        $this->dispatch('product-list-reset');
    }

    #[On('sortUpdated')]
    public function applySort(string $sortBy, string $sortDir): void
    {
        $this->sortBy = $sortBy;
        $this->sortDir = $sortDir;
        $this->page = 1;
        $this->dispatch('product-list-reset');
    }

    public function setPage(int $page): void
    {
        $this->page = max(1, $page);
        $this->dispatch('product-list-reset');
    }

    public function resetPage(): void
    {
        $this->page = 1;
    }

    #[Computed]
    public function products(): array
    {
        return app(TypeSenseService::class)->search(
            query: '',
            filters: [
                'category_id' => $this->category->id,
                ...$this->filters,
            ],
            page: $this->page,
            pageSize: 24,
            sortBy: $this->sortBy,
            sortDir: $this->sortDir
        );
    }

    public function render(): View
    {
        return view('livewire.catalog.product-list');
    }
}
