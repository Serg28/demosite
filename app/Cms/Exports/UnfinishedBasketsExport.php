<?php

namespace App\Cms\Exports;

use Illuminate\Contracts\View\View;
use Vis\Builder\Interfaces\Button;
use Vis\Builder\Services\ButtonBase;

class UnfinishedBasketsExport extends ButtonBase implements Button
{
    public function show(): View
    {
        return view('cms.buttons.export')
            ->with('route', route('admin.unfinished_basket.export'));
    }
}
