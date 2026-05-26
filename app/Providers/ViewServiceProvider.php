<?php

namespace App\Providers;

use App\Http\ViewComposers\BreadcrumbsCategoryComposer;
use App\Http\ViewComposers\SeoComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Реєструє View Composers.
 * Виокремлено з AppServiceProvider — за зразком linecore-demo (ComposerServiceProvider).
 */
class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer(['partials.seo', 'partials.seo_catalog'], SeoComposer::class);
        View::composer('catalog.index', BreadcrumbsCategoryComposer::class);
    }
}
