<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Characteristic;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * FacetService — facet data for the catalog.
 *
 * Disjunctive counts: for each char group, counts are computed WITHOUT that group's
 * filter applied (but WITH all other filters). This ensures options never disappear
 * when selected — users see useful "add/remove" counts.
 *
 * Sorting: numbers extracted from text sort numerically first, then alphabetical.
 * Selected options are always shown first within their group.
 *
 * All options are returned at once (no server-side pagination) — client handles
 * show-more and search via Alpine.js.
 */
class FacetService
{
    private const FACET_CACHE_TTL = 5; // minutes

    private const PRICE_CACHE_TTL = 60; // minutes

    /**
     * @param  array{characteristics: array<int, int[]|array{type: string, min: int|null, max: int|null}>, min_price: int|null, max_price: int|null}  $filters
     * @return array{price: array{min: int, max: int}, characteristics: array<int, array<string, mixed>>}
     */
    public function getFacetsForCategory(Category $category, array $filters = []): array
    {
        return [
            'price' => $this->getPriceFacet($category),
            'characteristics' => $this->getCharacteristicsFacets($category, $filters),
        ];
    }

    /**
     * Invalidate all facet cache for a category.
     */
    public function invalidate(Category $category): void
    {
        Cache::tags(['facets', "cat_{$category->id}"])->flush();
    }

    /** @return array{min: int, max: int} */
    private function getPriceFacet(Category $category): array
    {
        return Cache::tags(['facets', "cat_{$category->id}", 'price'])
            ->remember("facet_price:{$category->id}", now()->addMinutes(self::PRICE_CACHE_TTL), function () use ($category) {
                $row = DB::table('products')
                    ->where('category_id', $category->id)
                    ->where('is_active', true)
                    ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
                    ->first();

                return [
                    'min' => (int) ($row->min_price ?? 0),
                    'max' => (int) ($row->max_price ?? 0),
                ];
            });
    }

    /**
     * @param  array{characteristics: array<int, mixed>, min_price: int|null, max_price: int|null}  $filters
     * @return array<int, array<string, mixed>>
     */
    private function getCharacteristicsFacets(Category $category, array $filters): array
    {
        $chars = $category->characteristics()
            ->where('is_active', true)
            ->with(['options:id,characteristic_id,title,slug,priority'])
            ->orderBy('priority')
            ->get(['characteristics.id', 'characteristics.title', 'characteristics.slug', 'characteristics.is_range_type', 'characteristics.priority']);

        $activeCharFilters = array_filter(
            $filters['characteristics'] ?? [],
            fn ($v) => ! empty($v)
        );

        $facets = [];
        foreach ($chars as $char) {
            $facets[] = $this->buildCharacteristicFacet($char, $category, $filters, $activeCharFilters);
        }

        return $facets;
    }

    /** @return array<string, mixed> */
    private function buildCharacteristicFacet(
        Characteristic $char,
        Category $category,
        array $filters,
        array $activeCharFilters
    ): array {
        $filtersWithoutThisChar = array_filter(
            $activeCharFilters,
            fn ($id) => (int) $id !== (int) $char->id,
            ARRAY_FILTER_USE_KEY
        );

        $cacheKey = $this->buildCountCacheKey($category->id, $char->id, $filtersWithoutThisChar, $filters['min_price'] ?? null, $filters['max_price'] ?? null);

        $counts = Cache::tags(['facets', "cat_{$category->id}", "char_{$char->id}"])
            ->remember($cacheKey, now()->addMinutes(self::FACET_CACHE_TTL), function () use ($category, $char, $filtersWithoutThisChar, $filters) {
                return $this->getDisjunctiveCounts($category->id, $char->id, $filtersWithoutThisChar, $filters['min_price'] ?? null, $filters['max_price'] ?? null);
            });

        $activeOptIds = (array) ($activeCharFilters[$char->id] ?? []);

        $options = $char->options->map(function ($opt) use ($counts, $activeOptIds) {
            $count = $counts[$opt->id] ?? 0;

            return [
                'id' => $opt->id,
                'title' => $opt->t('title'),
                'slug' => $opt->slug,
                'count' => $count,
                'is_active' => in_array($opt->id, $activeOptIds, true),
                'is_disabled' => $count === 0,
            ];
        })->toArray();

        $options = $this->sortOptions($options);

        $facet = [
            'characteristic_id' => $char->id,
            'characteristic_slug' => $char->slug,
            'characteristic_title' => $char->t('title'),
            'is_range_type' => (bool) $char->is_range_type,
            'options' => $options,
        ];

        if ($char->is_range_type) {
            $numericValues = array_filter(
                array_map(fn ($o) => $this->extractNumber($o['title']), $options)
            );
            $facet['range_min'] = $numericValues ? (int) min($numericValues) : 0;
            $facet['range_max'] = $numericValues ? (int) max($numericValues) : 100;
        }

        return $facet;
    }

    /**
     * Disjunctive count: for char X, get option counts applying all filters EXCEPT X's own.
     * AND logic within groups — each selected option in other groups is a separate WHERE IN.
     *
     * @param  array<int, int[]>  $otherCharFilters
     * @return array<int, int> [option_id => count]
     */
    private function getDisjunctiveCounts(int $categoryId, int $charId, array $otherCharFilters, ?int $minPrice, ?int $maxPrice): array
    {
        $query = DB::table('product_characteristic_options as pco')
            ->join('products as p', 'p.id', '=', 'pco.product_id')
            ->where('p.category_id', $categoryId)
            ->where('p.is_active', true)
            ->whereIn('pco.characteristic_option_id', function ($q) use ($charId) {
                $q->select('id')->from('characteristic_options')->where('characteristic_id', $charId);
            });

        if ($minPrice !== null) {
            $query->where('p.price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('p.price', '<=', $maxPrice);
        }

        // AND between groups, AND within group (each option = separate subquery)
        foreach ($otherCharFilters as $optIds) {
            if (is_array($optIds) && isset($optIds['type'])) {
                continue; // range type — skip for now
            }
            foreach ((array) $optIds as $optId) {
                $query->whereIn('p.id', function ($q) use ($optId) {
                    $q->select('product_id')
                        ->from('product_characteristic_options')
                        ->where('characteristic_option_id', (int) $optId);
                });
            }
        }

        return $query
            ->selectRaw('pco.characteristic_option_id, COUNT(DISTINCT p.id) as cnt')
            ->groupBy('pco.characteristic_option_id')
            ->pluck('cnt', 'characteristic_option_id')
            ->map(fn ($v) => (int) $v)
            ->toArray();
    }

    /**
     * Sort options: active first → numeric values → alphabetical.
     * Extracts the first number from text (e.g. "до 15 л" → 15, "500 мл" → 500).
     *
     * @param  array<int, array<string, mixed>>  $options
     * @return array<int, array<string, mixed>>
     */
    private function sortOptions(array $options): array
    {
        usort($options, function (array $a, array $b) {
            // Active (selected) options always on top
            if ($a['is_active'] !== $b['is_active']) {
                return $a['is_active'] ? -1 : 1;
            }

            $aNum = $this->extractNumber($a['title']);
            $bNum = $this->extractNumber($b['title']);

            // Both numeric — sort numerically
            if ($aNum !== null && $bNum !== null) {
                return $aNum <=> $bNum;
            }

            // Numeric before text
            if ($aNum !== null) {
                return -1;
            }
            if ($bNum !== null) {
                return 1;
            }

            return mb_strtolower($a['title']) <=> mb_strtolower($b['title']);
        });

        return $options;
    }

    private function extractNumber(string $text): ?float
    {
        if (preg_match('/(\d+(?:[.,]\d+)?)/', $text, $m)) {
            return (float) str_replace(',', '.', $m[1]);
        }

        return null;
    }

    private function buildCountCacheKey(int $categoryId, int $charId, array $otherFilters, ?int $minPrice, ?int $maxPrice): string
    {
        ksort($otherFilters);
        $hash = md5(json_encode([$otherFilters, $minPrice, $maxPrice]));

        return "facet_counts:{$categoryId}:{$charId}:{$hash}";
    }
}
