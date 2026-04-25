<?php

namespace App\Http\ViewComposers\Universal;

use Illuminate\View\View;

class ClientLogoComposer
{
    public function compose(View $view): void
    {
        $logos = $view->block->logos()->active()->rememberForever()->cacheTags(['tree', 'block_clients_logo'])->get();

        $view->with(compact('logos'));
    }
}
