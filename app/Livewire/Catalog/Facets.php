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

    public function mount(Category $category, array $initialFilters = []): void
    {
        $this->category = $category;

        if (!empty($initialFilters)) {
            $this->currentFilters = $initialFilters;
        }
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

    /**
     * Maps characteristic/option IDs to URL slugs for pushUrlState().
     * Memoized per render cycle by #[Computed].
     */
    #[Computed]
    public function idToSlugMap(): array
    {
        $chars = $this->category->characteristics()
            ->where('is_active', true)
            ->with(['options:id,characteristic_id,slug'])
            ->get(['characteristics.id', 'characteristics.slug']);

        $charSlugs = [];
        $optSlugs = [];

        foreach ($chars as $char) {
            $charSlugs[$char->id] = $char->slug;
            foreach ($char->options as $opt) {
                $optSlugs[$opt->id] = $opt->slug;
            }
        }

        return ['chars' => $charSlugs, 'opts' => $optSlugs];
    }

    public function toggleOption(int $characteristicId, int $optionId): void
    {
        $selected = $this->currentFilters['characteristics'][$characteristicId] ?? [];

        if (in_array($optionId, $selected)) {
            $selected = array_values(array_filter($selected, fn ($id) => $id !== $optionId));
        } else {
            $selected[] = $optionId;
        }

        if (empty($selected)) {
            unset($this->currentFilters['characteristics'][$characteristicId]);
        } else {
            $this->currentFilters['characteristics'][$characteristicId] = $selected;
        }

        $this->dispatchFilters();
        $this->pushUrlState();
    }

    public function updated(string $property): void
    {
        if (str_starts_with($property, 'currentFilters.min_price') || str_starts_with($property, 'currentFilters.max_price')) {
            $this->dispatchFilters();
            $this->pushUrlState();
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
        $this->pushUrlState();
    }

    private function dispatchFilters(): void
    {
        $this->dispatch('filtersUpdated', filters: $this->currentFilters);
    }

    private function pushUrlState(): void
    {
        $slugMap = $this->idToSlugMap;
        $params = [];

        foreach ($this->currentFilters['characteristics'] as $charId => $optIds) {
            $charSlug = $slugMap['chars'][$charId] ?? null;
            if (!$charSlug || empty($optIds)) {
                continue;
            }
            $optSlugs = array_filter(array_map(fn ($id) => $slugMap['opts'][$id] ?? null, $optIds));
            if ($optSlugs) {
                $params[$charSlug] = implode(',', array_values($optSlugs));
            }
        }

        if ($this->currentFilters['min_price']) {
            $params['min_price'] = $this->currentFilters['min_price'];
        }
        if ($this->currentFilters['max_price']) {
            $params['max_price'] = $this->currentFilters['max_price'];
        }

        $this->dispatch('updateFilterUrl', params: $params);
    }

    public function render(): View
    {
        return view('livewire.catalog.facets');
    }
}
