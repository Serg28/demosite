<?php

namespace App\Http\ViewComposers\Universal;

use App\Models\Label;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ProductSaleSliderComposer
{
    public function compose(View $view): void
    {
        $saleProducts = Cache::tags(['products', 'sales_' . $view->block->id])
            ->remember('sales_product_' . $view->block->id, 86400, function () use ($view) {
                $labels = Label::where('id', 6)->first();
                $saleProducts = $view->block->saleProducts()->cardFields()->active()->available()->limit(15)->get();

                $saleProducts->each(function ($product, $key) use ($labels) {
                    return $product->labels->push($labels);
                });

                return $saleProducts;
            });

        //$saleProducts = $view->block->saleProducts()->active()->rememberForever()->cacheTags(['tree', 'products'])->get();

        $cacheKey = App::getLocale() . $view->id . md5($saleProducts) . md5($saleProducts->pluck('updated_at'). md5(serialize(app('user'))));
        $cacheTags = ['products'];

        $view->with(compact('saleProducts', 'cacheKey', 'cacheTags'));
    }


}
