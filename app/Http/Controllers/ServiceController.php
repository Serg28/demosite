<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Vis\Builder\TreeController;

class ServiceController extends TreeController
{
    public function index(): View
    {
        $page = $this->node->load('children.children');
        return view('service.index', compact('page'));
    }

    public function category(): View
    {
        $page = $this->node;

        $rubrics = $page->children;
        return view('service.category', compact('rubrics', 'page'));
    }

    public function page(): View
    {
        $page = $this->node;
        return view('service.page', compact('page'));
    }
}
