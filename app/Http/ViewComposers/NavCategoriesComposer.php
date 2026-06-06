<?php

declare(strict_types=1);

namespace App\Http\ViewComposers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Надає $navCategories (кореневі + дочірні) у shop-layout.
 * Кешується 5 хвилин.
 */
class NavCategoriesComposer
{
    public function compose(View $view): void
    {
        $view->with('navCategories', Cache::remember('nav_categories', 300, fn () =>
            Category::query()
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('priority')])
                ->orderBy('priority')
                ->limit(20)
                ->get()
        ));
    }
}
