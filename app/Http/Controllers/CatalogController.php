<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function show(Category $category, Request $request): View
    {
        $initialFilters = $this->resolveFiltersFromUrl($request, $category);

        return view('catalog.index', compact('category', 'initialFilters'));
    }

    private function resolveFiltersFromUrl(Request $request, Category $category): array
    {
        $empty = ['characteristics' => [], 'min_price' => null, 'max_price' => null];
        $query = $request->query();

        if (empty($query)) {
            return $empty;
        }

        $chars = $category->characteristics()
            ->where('is_active', true)
            ->with(['options:id,characteristic_id,slug'])
            ->get(['characteristics.id', 'characteristics.slug']);

        $characteristics = [];

        foreach ($chars as $char) {
            $paramValue = $query[$char->slug] ?? null;
            if (!$paramValue) {
                continue;
            }

            $optSlugs = array_filter(explode(',', (string) $paramValue));
            $optMap = $char->options->pluck('id', 'slug')->toArray();
            $optIds = array_values(array_filter(array_map(fn ($slug) => $optMap[$slug] ?? null, $optSlugs)));

            if ($optIds) {
                $characteristics[$char->id] = $optIds;
            }
        }

        $minPrice = isset($query['min_price']) && is_numeric($query['min_price']) ? (int) $query['min_price'] : null;
        $maxPrice = isset($query['max_price']) && is_numeric($query['max_price']) ? (int) $query['max_price'] : null;

        if (empty($characteristics) && !$minPrice && !$maxPrice) {
            return $empty;
        }

        return ['characteristics' => $characteristics, 'min_price' => $minPrice, 'max_price' => $maxPrice];
    }
}
