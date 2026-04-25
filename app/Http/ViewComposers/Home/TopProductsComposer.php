<?php

namespace App\Http\ViewComposers\Home;

use App\Models\Product;
use Illuminate\View\View;

class TopProductsComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('TopProductsComposer', 'Time for TopProductsComposer');
        $topProducts = Product::latest('created_at')->active()->take(8)
            ->rememberForever()->cacheTags(['products'])
            ->get();

        $view->with(compact('topProducts'));
        debugbar()->stopMeasure('TopProductsComposer');
    }
}
