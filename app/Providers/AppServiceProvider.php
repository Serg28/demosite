<?php

namespace App\Providers;

use App\Http\ViewComposers\SeoComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer(['partials.seo', 'partials.seo_catalog'], SeoComposer::class);
    }
}
