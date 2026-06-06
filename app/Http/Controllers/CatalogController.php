<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesSort;
use App\Models\Category;
use App\Services\FilterUrlService;
use App\Services\TypeSenseService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    use ResolvesSort;

    public function show(Category $category, Request $request, string $filters = ''): View
    {
        $urlService = app(FilterUrlService::class);
        $slugMap = $urlService->buildSlugMap($category);
        $initialFilters = $urlService->parseFilterPath($filters, $slugMap);
        $basePath = rtrim((string) parse_url($category->getUrl(), PHP_URL_PATH), '/');

        [$sortBy, $sortDir] = $this->resolveSortParam($request->get('sort'));
        $initialPage = max(1, (int) $request->get('page', 1));

        $count = app(TypeSenseService::class)->count(filters: [
            'category_id'     => $category->id,
            'characteristics' => $initialFilters['characteristics'],
            'min_price'       => $initialFilters['min_price'],
            'max_price'       => $initialFilters['max_price'],
            'in_stock'        => $initialFilters['in_stock'],
        ]);

        return view('catalog.index', compact('category', 'initialFilters', 'basePath', 'filters', 'count', 'sortBy', 'sortDir', 'initialPage'));
    }
}
