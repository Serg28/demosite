<?php

namespace App\Http\ViewComposers\Universal;

use App\Models\Product;
use Illuminate\View\View;

class ProductHitAutoSliderComposer
{
    public function compose(View $view): void
    {
        //$hitsProducts = Product::where('is_hit', 2)
        $hitsProducts = Product::where('to_main', '=', 1)
            ->active()
            //->orderBy('count_views', 'desc')
            ->take(5)
            //->rememberForever()->cacheTags(['products'])
            //->get()->shuffle();
            ->get();

        $hitsProductsByLabel = Product::whereHas('labels', function ($q) {
            $q->where('label_id', '=', 2);
        })->active()
           ->take(10)
            ->rememberForever()->cacheTags(['products'])
           ->get();

        $view->with(compact('hitsProducts', 'hitsProductsByLabel'));
    }
}
