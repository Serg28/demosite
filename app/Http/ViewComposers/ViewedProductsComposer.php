<?php

namespace App\Http\ViewComposers;

use App\Models\Product;
use Illuminate\View\View;

class ViewedProductsComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('ViewedProductsComposer', 'Time for ViewedProductsComposer');
        $viewedProducts = (new Product())->getView();
        $count = 0;
        if ($viewedProducts) {
            $viewedProducts->splice(8);
            $count = count($viewedProducts);
        }

        $view->with(compact('viewedProducts', 'count'));
        debugbar()->stopMeasure('ViewedProductsComposer');
    }
}

