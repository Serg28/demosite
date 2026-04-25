<?php

namespace App\Http\ViewComposers\Home;

use App\Models\Product;
use Illuminate\View\View;

class NewProductsComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('NewProductsComposer', 'Time for NewProductsComposer');
        $newProducts = Product::latest('created_at')->active()->take(8)
            ->rememberForever()->cacheTags(['products'])
            ->cardFields()
            ->get();

        $view->with(compact('newProducts'));
        debugbar()->stopMeasure('NewProductsComposer');
    }
}
