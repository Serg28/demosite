<?php

namespace App\Http\Controllers;

use App\Models\MenuSeocatalog;
use Illuminate\Contracts\View\View;
use Vis\Builder\TreeController;

class SeocatalogController extends TreeController
{
    // Если категории - документы в дереве статей
    public function category(): View
    {
        $page = $this->node;
        //$rubrics = $page->children;

        $rubrics = $page->load([
            'children' => function ($query) {
                $query->active();
            }
        ])->children;

        return view('category.seocatalog', compact('rubrics', 'page'));
    }

    // Если категории - пункты меню SEO-каталог
    /*public function category(): View
    {
        $page = $this->node;
        $rubrics = (new MenuSeocatalog())->getMenu();
        return view('seocatalog.catalog', compact('rubrics', 'page'));
    }*/
}
