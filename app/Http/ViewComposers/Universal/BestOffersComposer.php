<?php

namespace App\Http\ViewComposers\Universal;

use App\Models\Product;
use Illuminate\View\View;

class BestOffersComposer
{
    public function compose(View $view): void
    {
        $categoryId = setting('blok-luchshie-predlozheniya-id');
        $bestOffersProducts = Product::whereHas('categories', function ($q) use ($categoryId) {
            $q->where('category_product.category_id', $categoryId);
        })
            ->orderBy('count_views', 'desc')
            ->cardFields()
            ->active()
            ->take(8)
            ->rememberForever()
            ->cacheTags(['products'])->get();

        $view->with(compact('bestOffersProducts'));
    }
}