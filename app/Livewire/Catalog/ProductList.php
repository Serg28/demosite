<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use App\Services\TypeSenseService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public Category $category;

    public array $filters = [];

    public string $sortBy = 'priority';

    public string $sortDir = 'desc';

    public function __construct(private TypeSenseService $typeSearchService)
    {
        parent::__construct();
    }

    public function mount(Category $category, array $filters = [], string $sortBy = 'priority', string $sortDir = 'desc'): void
    {
        $this->category = $category;
        $this->filters = $filters;
        $this->sortBy = $sortBy;
        $this->sortDir = $sortDir;
    }

    #[Computed]
    public function products()
    {
        $page = $this->getPage();

        $results = $this->typeSearchService->search(
            query: '',
            filters: [
                'category_id' => $this->category->id,
                ...$this->filters
            ],
            page: $page,
            pageSize: 24,
            sortBy: $this->sortBy,
            sortDir: $this->sortDir
        );

        return $results;
    }

    public function render()
    {
        return view('livewire.catalog.product-list', [
            'products' => $this->products,
        ]);
    }
}
