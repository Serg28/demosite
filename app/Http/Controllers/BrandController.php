<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Characteristic as CharacteristicModel;
use App\Models\CharacteristicOption;
use App\Models\Product;
use App\Services\ElasticSearch;
use App\Services\Sorting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BrandController extends BaseController
{
    public $showCount = 16;

    public function __construct()
    {
        $this->showCount = request('show') ? request('show') : $this->showCount;
    }

    public function getOptions(CharacteristicModel $characteristic): View
    {
        return view('brand.index', compact('characteristic'));
    }

    public function getProducts(
        string $characteristic,
        CharacteristicOption $characteristicOption,
        Sorting $sorting,
        ElasticSearch $elasticsearch,
        Request $request
    ) {
        $characteristicOption = $characteristicOption->slug($characteristicOption->slug)
            ->where('characteristic_id', 15010)
            ->first();

        $products_query = $characteristicOption->products()
            ->notNullPrice()
            ->available();

        $categoryIds = $products_query->pluck('category_id')->concat(
            CategoryProduct::whereIn('product_id', $products_query->pluck('products.id'))->pluck('category_id')
        )->unique()->toArray();


        $categories = Category::whereIn('id', $categoryIds)
            ->select(['id', 'title'])  // Загрузите только нужные свойства категорий
            ->defaultOrder()
            ->active()
            ->get();

        $brand = Brand::where('slug', $characteristicOption->slug)->first();

        $products = $products_query->cardFields()->sortedBy($request->get('sort'))->paginate($this->showCount);

        return view('brand.products', compact('products', 'characteristicOption', 'sorting', 'brand', 'categories', 'characteristic'));
    }

}
