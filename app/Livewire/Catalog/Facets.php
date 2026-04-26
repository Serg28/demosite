<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use App\Services\FacetService;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Facets extends Component
{
    public Category $category;

    public array $currentFilters = [
        'characteristics' => [],
        'min_price' => null,
        'max_price' => null,
    ];

    public array $expandedFacets = [];

    public function mount(Category $category): void
    {
        $this->category = $category;
    }

    #[Computed]
    public function facets(): array
    {
        return app(FacetService::class)->getFacetsForCategory(
            $this->category,
            $this->expandedFacets,
            $this->currentFilters
        );
    }

    public function toggleOption(int $characteristicId, int $optionId): void
    {
        $selected = $this->currentFilters['characteristics'][$characteristicId] ?? [];

        if (in_array($optionId, $selected)) {
            $selected = array_values(array_filter($selected, fn ($id) => $id !== $optionId));
        } else {
            $selected[] = $optionId;
        }

        $this->currentFilters['characteristics'][$characteristicId] = $selected;
        $this->dispatchFilters();
    }

    public function updated(string $property): void
    {
        if (str_starts_with($property, 'currentFilters.min_price') || str_starts_with($property, 'currentFilters.max_price')) {
            $this->dispatchFilters();
        }
    }

    public function toggleFacet(int $characteristicId): void
    {
        if (in_array($characteristicId, $this->expandedFacets)) {
            $this->expandedFacets = array_values(array_filter($this->expandedFacets, fn ($id) => $id !== $characteristicId));
        } else {
            $this->expandedFacets[] = $characteristicId;
        }
    }

    public function clearFilters(): void
    {
        $this->currentFilters = ['characteristics' => [], 'min_price' => null, 'max_price' => null];
        $this->dispatchFilters();
    }

    private function dispatchFilters(): void
    {
        $this->dispatch('filtersUpdated', filters: $this->currentFilters);
    }

    public function render(): View
    {
        return view('livewire.catalog.facets');
    }
}
