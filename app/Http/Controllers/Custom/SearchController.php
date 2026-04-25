<?php

namespace App\Http\Controllers\Custom;

use App\Http\Controllers\Controller;
use App\Jobs\SaveStatisticSearch;
use App\Models\Product;
use App\Services\ElasticSearch;
use App\Services\ElasticsearchProductService;
use App\Services\LanguageCorrect;
use App\Services\Sorting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public mixed $showCount = 16;

    protected ElasticsearchProductService $productService;

    public function __construct(ElasticsearchProductService $productService)
    {
        $this->productService = $productService;
        $this->showCount = request('show') ?: $this->showCount;
    }

    public function index(Request $request, Sorting $searchSorting): View
    {
        $query = $request->get('q');

        $products = $this->productService->searchProducts($query, $request->get('sort'))->paginate($this->showCount);

        if (!$products->total()) {
            $query = $this->prepareQuery($request->get('q'));
            $products = $this->productService->searchProducts($query, $request->get('sort'))->paginate($this->showCount);
        }

        if ($query) {
            $this->saveStatisticSearch($request);
        }

        $correctWord = ($request->get('q')!==$query && $products->total()) ? $query : '';
        $oldWord = $request->get('q');
        $query = ($request->get('q')!==$query && $products->total()) ? $query : $request->get('q');
        $request->merge(['q' => $query]);

        return view('search.index', compact('products', 'searchSorting', 'correctWord', 'oldWord', 'query'));
    }

    public function live(Request $request): JsonResponse
    {
        $query = $request->get('q');

        $products = $this->productService->searchProducts($query, $request->get('sort'))->limit(3)->get();

        if (!$products->count()) {
            $query = $this->prepareQuery($request->get('q'));
            $products = $this->productService->searchProducts($query, $request->get('sort'))->limit(3)->get();
        }

        $this->saveStatisticSearch($request);

        return response()->json(
            [
                'status' => 'success',
                'html' => view('search.live', compact('products'))->render(),
            ]
        );
    }

    private function saveStatisticSearch(Request $request): void
    {
        SaveStatisticSearch::dispatch($request->get('q'), app('user'));
    }

    /*
    private function sortOrderInit(): void
    {
        $collectUrls = explode('/', request()->path());

        foreach ($collectUrls as $param) {
            if (strpos($param, '=')) {
                $paramSortOrder = explode('=', $param);

                if ($paramSortOrder[0] == 'show') {
                    $this->countShow = $paramSortOrder[1];
                } elseif ($paramSortOrder[0] == 'sort') {
                    $this->sorting = $paramSortOrder[1];
                }
            }
        }
    }*/

    private function prepareQuery($query)
    {
        try {
            return LanguageCorrect::keyboardLayoutConvertEnRuAuto($query);
        } catch (\Exception $e) {
            return $query;
        }
    }

}
