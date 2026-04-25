<?php

namespace App\Cms\Imports;

use Illuminate\Contracts\View\View;
use Vis\Builder\Services\Import;

class ImportProducts extends Import
{
    public function show(): View
    {
        $nameDefinition = mb_strtolower(class_basename($this->listing->getDefinition()));

        return view('cms.buttons.import_products', compact('nameDefinition'));
    }
}
