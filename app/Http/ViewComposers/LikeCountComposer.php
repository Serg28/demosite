<?php

namespace App\Http\ViewComposers;

use App\Models\Product;
use Illuminate\View\View;

class LikeCountComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('LikeCountComposer', 'Time for LikeCountComposer');
        $favoritesCount = (app('user')) ? Product::whereLikedBy(app('user')->id)->count() : '';

        $view->with(compact('favoritesCount'));
        debugbar()->stopMeasure('LikeCountComposer');
    }
}
