<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use App\Services\FacetService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Facets extends Component
{
    public Category $category;

    public array $currentFilters = [];

    public array $expandedFacets = [];

    public function __construct(private FacetService $facetService)
    {
        parent::__construct();
    }

    public function mount(Category $category, array $filters = []): void
    {
        $this->category = $category;
        $this->currentFilters = $filters;
    }

    #[Computed]
    public function facets(): array
    {
        return $this->facetService->getFacetsForCategory(
            $this->category,
            $this->expandedFacets,
            $this->currentFilters
        );
    }

    public function toggleFacet(int $characteristicId): void
    {
        if (in_array($characteristicId, $this->expandedFacets)) {
            $this->expandedFacets = array_filter($this->expandedFacets, fn($id) => $id !== $characteristicId);
        } else {
            $this->expandedFacets[] = $characteristicId;
        }
    }

    public function render()
    {
        return view('livewire.catalog.facets');
    }
}
