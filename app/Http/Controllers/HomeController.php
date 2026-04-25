<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;
use Vis\Builder\TreeController;

class HomeController extends TreeController
{
    public function index(): View
    {
        $page = $this->node;

        $blocks = $page->load([
            'blocks' => function ($query) {
                $query->active()->with(['favoriteCategories', 'contactsWithMap'/*, 'description', 'blockButtons'*/]);
            }
        ]);

        $is_home = 1;

        $sliderBlock = $page->blocks->firstWhere('template', 'banner_slider')->bannersSlider ?? null;

        $whyWe = $page->blocks->firstWhere('template', 'block_home_why_we');
        $sliderCatalog = $page->blocks->firstWhere('template', 'home_catalog_show');
        $sliderBrand = $page->blocks->firstWhere('template', 'brand_select_list');
        $lastNews = $page->blocks->firstWhere('template', 'last_news');

        $hitsProducts = Product::orderBy('count_views', 'desc')
            ->cardFields()
            ->active()
            ->limit(12)
            ->rememberForever()->cacheTags(['products'])
            ->get();
        return view('home.index', compact('page', 'blocks', 'is_home', 'sliderBlock','whyWe', 'lastNews', 'sliderCatalog', 'sliderBrand', 'hitsProducts'));
    }
}
