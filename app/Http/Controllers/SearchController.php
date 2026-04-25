<?php

namespace App\Http\Controllers;

use App\Jobs\SaveStatisticSearch;
use App\Services\ElasticSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{

    public function index()
    {
        return view('search.index');
        //return view('search.index', compact('products', 'searchSorting', 'correctWord', 'oldWord', 'query'));
    }



    /*
    public function index(Request $request, ElasticSearch $elasticsearch): View
    {
        $products = $elasticsearch->search($request->get('q'))->paginate(12);

        $this->saveStatisticSearch($request);

        return view('search.index', compact('products'));
    }

    public function live(Request $request, ElasticSearch $elasticsearch): JsonResponse
    {
        $products = $elasticsearch->search($request->get('q'))->limit(2)->get();

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
    */
}
