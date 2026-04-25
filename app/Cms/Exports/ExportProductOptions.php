<?php

namespace App\Cms\Exports;

use Illuminate\Contracts\View\View;
use Vis\Builder\Interfaces\Button;
use Vis\Builder\Services\ButtonBase;

class ExportProductOptions extends ButtonBase implements Button
{
    public function show(): View
    {
        return view('cms.buttons.export_product_options')
            ->with('route', route('admin.products.options.export'));
    }
}
