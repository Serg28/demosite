<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Jorenvh\Share\Share;

class ShareSocialComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('ShareSocialComposer', 'Time for ShareSocialComposer');
        $share = new Share();

        $social = $share->currentPage()
            ->facebook()
            ->twitter()
            ->whatsapp()
            ->linkedin()
            ->pinterest()
           ->getRawLinks();

        $view->with(compact('social'));
        debugbar()->stopMeasure('ShareSocialComposer');
    }
}
