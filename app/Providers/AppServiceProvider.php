<?php

namespace App\Providers;

use App\Contracts\PricingStrategy;
use App\Http\ViewComposers\BreadcrumbsCategoryComposer;
use App\Http\ViewComposers\SeoComposer;
use App\Models\User;
use App\Services\CurrencyService;
use App\Services\Pricing\DefaultPricingStrategy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CurrencyService::class);
        $this->app->bind(PricingStrategy::class, DefaultPricingStrategy::class);

        // Octane-safe per-request user binding
        $this->app->scoped('user', static fn (): ?User => Auth::user());
    }

    public function boot(): void
    {
        View::composer(['partials.seo', 'partials.seo_catalog'], SeoComposer::class);
        View::composer('catalog.index', BreadcrumbsCategoryComposer::class);
    }
}
