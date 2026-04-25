<?php

namespace App\Cms\Exports;

use Illuminate\Contracts\View\View;
use Vis\Builder\Interfaces\Button;
use Vis\Builder\Services\ButtonBase;

class ExportProducts extends ButtonBase implements Button
{
    public function show(): View
    {
        return view('cms.buttons.export_products')
            ->with('route', route('admin.products.export'));
    }
}
