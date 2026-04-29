<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Concerns\ResolvesSort;
use App\Http\Controllers\Controller;
use App\Services\FacetService;
use App\Services\FilterUrlService;
use App\Services\OptionSearchService;
use App\Services\TypeSenseService;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CatalogController extends Controller
{
    use ResolvesSort;

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

    public function getProductsHtml(Category $category, Request $request, FilterUrlService $urlService): \Illuminate\Http\Response
    {
        $filterPath = (string) $request->get('filter_path', '');
        $page       = max(1, (int) $request->get('page', 1));
        $sortParam  = $request->get('sort');
        $raw        = (string) $request->get('page_base', '/');
        $pageBase   = str_starts_with($raw, '/') ? $raw : '/';

        [$sortBy, $sortDir] = $this->resolveSortParam($sortParam);

        $initialFilters = $urlService->parseFilterPath($filterPath, $urlService->buildSlugMap($category));

        $perPage = config('catalog.per_page', 24);

        $result = $this->typeSearchService->search(
            query: '',
            filters: ['category_id' => $category->id, ...$initialFilters],
            page: $page,
            pageSize: $perPage,
            sortBy: $sortBy,
            sortDir: $sortDir
        );

        $paginator = (new LengthAwarePaginator(
            items: $result['products'] ?? [],
            total: $result['total'] ?? 0,
            perPage: $perPage,
            currentPage: $page,
        ))
            ->withPath($pageBase)
            ->appends(array_filter(['sort' => $sortParam]));

        return response()->view('partials.catalog.products-html', [
            'products'  => $result['products'] ?? [],
            'paginator' => $paginator,
        ]);
    }

    public function getRangeStats(int $characteristicId): JsonResponse
    {
        $characteristic = \App\Models\Characteristic::findOrFail($characteristicId);
        $stats = $this->optionSearchService->getRangeStats($characteristic);

        return response()->json(['data' => $stats]);
    }
}
