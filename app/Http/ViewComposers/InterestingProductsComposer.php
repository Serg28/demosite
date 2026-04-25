<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class InterestingProductsComposer
{
    public function compose(View $view): void
    {
        $interestingProducts = $view->page->interestingProducts()->active()
            ->rememberForever()->cacheTags(['products'])
            ->get();

        $view->with(compact('interestingProducts'));
    }
}
