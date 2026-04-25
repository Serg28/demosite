<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerBaseServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function boot(): void
    {
        /*View::composer('base.partials.header', 'App\Http\ViewComposers\HeaderMenuComposer');
        View::composer('base.partials.menu.footer', 'App\Http\ViewComposers\FooterComposer');
        View::composer('base.news.last_news', 'App\Http\ViewComposers\LastNewsComposer');
        View::composer('base.partials.change_lang', 'App\Http\ViewComposers\LanguagesComposer');
        View::composer('base.partials.breadcrumbs', 'App\Http\ViewComposers\BreadcrumbsComposer');
        View::composer('base.partials.viewed_products', 'App\Http\ViewComposers\ViewedProductsComposer');

        View::composer('base.home.partials.slider', 'App\Http\ViewComposers\Home\SliderComposer');
        View::composer('base.home.partials.top_products', 'App\Http\ViewComposers\Home\TopProductsComposer');
        View::composer('base.home.partials.hits_products', 'App\Http\ViewComposers\Home\HitsProductsComposer');

        View::composer('base.profile.partials.menu', 'App\Http\ViewComposers\Profile\MenuComposer');

        View::composer('base.partials.compare_count', 'App\Http\ViewComposers\CompareCountComposer');
        View::composer('base.partials.cart_header', 'App\Http\ViewComposers\CartHeaderComposer');
        View::composer('base.category.filter', 'App\Http\ViewComposers\FilterComposer');
        View::composer('base.category.partials.select_filter', 'App\Http\ViewComposers\FilterSelectComposer');
        View::composer('base.product.partials.interesting_products', 'App\Http\ViewComposers\InterestingProductsComposer');*/
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        //
    }
}
