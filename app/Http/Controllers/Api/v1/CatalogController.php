<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\FacetService;
use App\Services\OptionSearchService;
use App\Services\TypeSenseService;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __construct(
        private TypeSenseService $typeSearchService,
        private FacetService $facetService,
        private OptionSearchService $optionSearchService,
    ) {}

    public function getFacets(Category $category, Request $request): JsonResponse
    {
        $expandedFacets = $request->input('expanded', []);
        $currentFilters = $request->input('filters', []);

        $facets = $this->facetService->getFacetsForCategory(
            $category,
            $expandedFacets,
            $currentFilters
        );

        return response()->json(['data' => $facets]);
    }

    public function getExpandedFacet(Category $category, int $characteristicId, Request $request): JsonResponse
    {
        $page = $request->input('page', 1);
        $currentFilters = $request->input('filters', []);

        $facet = $this->facetService->getExpandedFacet(
            $category,
            $characteristicId,
            $page,
            50,
            $currentFilters
        );

        return response()->json(['data' => $facet]);
    }

    public function searchOptions(int $characteristicId, Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 15);

        $options = $this->optionSearchService->searchOptions($characteristicId, $query, $limit);

        return response()->json(['data' => $options]);
    }

    public function getProducts(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $filters = $request->input('filters', []);
        $page = $request->input('page', 1);
        $pageSize = $request->input('per_page', 24);
        $sortBy = $request->input('sort_by', 'priority');
        $sortDir = $request->input('sort_dir', 'desc');

        $results = $this->typeSearchService->search(
            $query,
            $filters,
            $page,
            $pageSize,
            $sortBy,
            $sortDir
        );

        return response()->json($results);
    }

    public function getRangeStats(int $characteristicId): JsonResponse
    {
        $characteristic = \App\Models\Characteristic::findOrFail($characteristicId);
        $stats = $this->optionSearchService->getRangeStats($characteristic);

        return response()->json(['data' => $stats]);
    }
}
