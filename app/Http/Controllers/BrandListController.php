<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\View\View;
use Vis\Builder\TreeController;

class BrandListController extends TreeController
{
    public function index(): View
    {
        $page = $this->node;
        $brands = Brand::active()->orderPriority()
            ->rememberForever()->cacheTags(['brands'])
            ->paginate(18);

        return view('brand.index', compact('brands', 'page'));
    }
}
