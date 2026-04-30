<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use App\Services\FacetService;
use App\Services\FilterUrlService;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Facets extends Component
{
    public Category $category;

    #[Locked]
    public string $basePath = '';

    public string $filtersPath = '';

    public function mount(Category $category, string $basePath = '', string $initialFiltersPath = '', array $initialFilters = []): void
    {
        $this->category = $category;
        $this->basePath = $basePath ?: rtrim((string) parse_url($category->getUrl(), PHP_URL_PATH), '/');
        $this->filtersPath = $initialFiltersPath;
    }

    /**
     * Called by JS when browser URL changes (filter click or popstate).
     * $page is forwarded from popstate so filters + page are restored in one round-trip.
     */
    #[On('filter-changed')]
    public function onFilterChanged(string $path, int $page = 1): void
    {
        $prefix = rtrim((string) parse_url($this->category->getUrl(), PHP_URL_PATH), '/').'/';

        $this->filtersPath = str_starts_with($path, $prefix)
            ? substr($path, strlen($prefix))
            : '';

        $this->dispatch('filtersUpdated', filters: $this->activeFilters, page: $page);
    }

    /** @return array{characteristics: array<int, int[]>, min_price: int|null, max_price: int|null} */
    #[Computed]
    public function activeFilters(): array
    {
        return app(FilterUrlService::class)->parseFilterPath($this->filtersPath, $this->slugMap);
    }

    /** @return array<string, array{char_id: int, is_range_type: bool, options: array<string, int>}> */
    #[Computed]
    public function slugMap(): array
    {
        return app(FilterUrlService::class)->buildSlugMap($this->category);
    }

    /** @return array{price: array{min: int, max: int}, characteristics: array<int, array<string, mixed>>} */
    #[Computed]
    public function facets(): array
    {
        $raw = app(FacetService::class)->getFacetsForCategory($this->category, $this->activeFilters);

        return $this->enrichWithUrls($raw);
    }

    /** @return array<int, array{char_title: string, opt_title: string, remove_url: string}> */
    #[Computed]
    public function activeFilterTags(): array
    {
        $urlService = app(FilterUrlService::class);
        $tags = [];

        $active = $this->activeFilters;
        $chars = $this->facets['characteristics'] ?? [];

        foreach ($chars as $facet) {
            $activeOptIds = (array) ($active['characteristics'][$facet['characteristic_id']] ?? []);
            foreach ($facet['options'] as $opt) {
                if (! in_array($opt['id'], $activeOptIds, true)) {
                    continue;
                }
                $tags[] = [
                    'char_title' => $facet['characteristic_title'],
                    'opt_title' => $opt['title'],
                    'remove_url' => $urlService->buildOptionUrl($this->basePath, $this->filtersPath, $facet['characteristic_slug'], $opt['slug']),
                ];
            }
        }

        if ($active['min_price'] || $active['max_price']) {
            $tags[] = [
                'char_title' => __t('Ціна'),
                'opt_title' => ($active['min_price'] ?? '?').' – '.($active['max_price'] ?? '?'),
                'remove_url' => $urlService->buildRangeUrl($this->basePath, $this->filtersPath, 'price', null, null),
            ];
        }

        return $tags;
    }

    public function render(): View
    {
        return view('livewire.catalog.facets');
    }

    /**
     * Enrich raw facets with toggle_url and seo_url per option.
     *
     * @param  array{price: array{min: int, max: int}, characteristics: array<int, array<string, mixed>>}  $raw
     * @return array{price: array{min: int, max: int}, characteristics: array<int, array<string, mixed>>, clear_url: string}
     */
    private function enrichWithUrls(array $raw): array
    {
        $urlService = app(FilterUrlService::class);

        foreach ($raw['characteristics'] as &$facet) {
            foreach ($facet['options'] as &$option) {
                $toggleUrl = $urlService->buildOptionUrl(
                    $this->basePath,
                    $this->filtersPath,
                    $facet['characteristic_slug'],
                    $option['slug']
                );
                $option['toggle_url'] = $toggleUrl;
                $option['seo_url'] = $toggleUrl;
            }
            unset($option);

            if ($facet['is_range_type']) {
                $active = $this->activeFilters;
                $rangeData = $active['characteristics'][$facet['characteristic_id']] ?? null;
                $facet['range_current_min'] = is_array($rangeData) && isset($rangeData['min']) ? $rangeData['min'] : null;
                $facet['range_current_max'] = is_array($rangeData) && isset($rangeData['max']) ? $rangeData['max'] : null;
                $facet['range_url_base'] = $this->basePath;
                $facet['range_filters_path'] = $this->filtersPath;
            }
        }
        unset($facet);

        $raw['clear_url'] = $urlService->buildClearUrl($this->basePath);
        $raw['price']['current_min'] = $this->activeFilters['min_price'];
        $raw['price']['current_max'] = $this->activeFilters['max_price'];

        return $raw;
    }
}
