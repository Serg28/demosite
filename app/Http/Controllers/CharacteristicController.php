<?php

namespace App\Http\Controllers;

use App\Models\Characteristic as CharacteristicModel;
use App\Models\CharacteristicOption;
use App\Services\ElasticSearch;
use App\Services\Sorting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CharacteristicController extends BaseController
{
    public function getOptions(CharacteristicModel $characteristic): View
    {
        return view('characteristic.index', compact('characteristic'));
    }

    public function getProducts(
        string $characteristic,
        CharacteristicOption $characteristicOption,
        Sorting $sorting,
        ElasticSearch $elasticsearch,
        Request $request
    ) {
        $products = $elasticsearch->search(
            $characteristicOption->products()->getQuery(),
            $request->get('q'),
            $request->get('sort')
        )->paginate(12);

        return view('characteristic.products', compact('products', 'characteristicOption', 'sorting'));
    }
}
