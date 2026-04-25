<?php

namespace App\Http\ViewComposers\Home;

use App\Models\Product;
use Illuminate\View\View;

class HitsProductsComposer
{
    public function compose(View $view): void
    {
        $hitsProducts = Product::orderBy('count_views', 'desc')
            ->cardFields()
            ->active()
            ->take(8)
            ->rememberForever()->cacheTags(['products'])
            ->get();

        $view->with(compact('hitsProducts'));
    }
}
