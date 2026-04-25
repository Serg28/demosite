<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function show(Category $category): View
    {
        return view('catalog.index', compact('category'));
    }
}
