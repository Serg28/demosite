<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\FilterUrlService;
use App\Services\TypeSenseService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function show(Category $category, Request $request, string $filters = ''): View
    {
        $urlService = app(FilterUrlService::class);
        $slugMap = $urlService->buildSlugMap($category);
        $initialFilters = $urlService->parseFilterPath($filters, $slugMap);
        $basePath = '/catalog/'.$category->slug;

        $count = app(TypeSenseService::class)->count(filters: [
            'category_ids' => [$category->id],
            'characteristics' => $initialFilters['characteristics'],
            'min_price' => $initialFilters['min_price'],
            'max_price' => $initialFilters['max_price'],
        ]);

        return view('catalog.index', compact('category', 'initialFilters', 'basePath', 'filters', 'count'));
    }
}
