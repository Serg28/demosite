<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function boot(): void
    {
        /*View::composer([
            'partials.header_menu',
            'home.partials.catalog_mobile',
            'home.partials.first_screen_menu'
            , 'partials.popups'
        ], 'App\Http\ViewComposers\HeaderMenuComposer');*/
        View::composer(['partials.seo', 'partials.seo_for_catalog', 'partials.seo_promotions'/*, 'partials.seo_characteristics'*/], 'App\Http\ViewComposers\SeoComposer');
        //View::composer('partials.footer', 'App\Http\ViewComposers\FooterComposer');
        //View::composer('partials.popups', 'App\Http\ViewComposers\MenusComposer');
        View::composer([/*'news.last_news', 'universal.blocks.last_news',*/ 'home.blocks.last_news'], 'App\Http\ViewComposers\LastNewsComposer');
        //View::composer(['partials.change_lang', 'layouts.default'], 'App\Http\ViewComposers\LanguagesComposer');
        View::composer([/*'partials.change_lang','partials.change_lang_mobile', */ 'layouts.default'], 'App\Http\ViewComposers\LanguagesComposer');
        View::composer('partials.breadcrumb', 'App\Http\ViewComposers\BreadcrumbsComposer');
        View::composer('partials.breadcrumb_simple', 'App\Http\ViewComposers\BreadcrumbsSimpleComposer');
        //==
        //View::composer('partials.breadcrumb_options', 'App\Http\ViewComposers\BreadcrumbsOptionsComposer');
        //==

        View::composer(['partials.blocks.viewed_products', 'livewire.favorite.content'], 'App\Http\ViewComposers\ViewedProductsComposer');

        View::composer(['product.partials.similar_products'], 'App\Http\ViewComposers\SimilarProductsComposer');


        View::composer('partials.blocks.new_products', 'App\Http\ViewComposers\Home\NewProductsComposer');

        //View::composer('home.partials.banner', 'App\Http\ViewComposers\Home\SliderComposer');
        //View::composer('home.partials.populars', 'App\Http\ViewComposers\Home\TopProductsComposer');
        //View::composer('home.partials.hits', 'App\Http\ViewComposers\Home\HitsProductsComposer');
        //View::composer('home.partials.brands', 'App\Http\ViewComposers\Home\BrandsComposer');

        //View::composer(['profile.partials.menu','profile.partials.menu_mobile', 'profile.partials.menu_mobile_index'], 'App\Http\ViewComposers\Profile\MenuComposer');

        //View::composer(['partials.like_count'], 'App\Http\ViewComposers\LikeCountComposer');
        //View::composer(['partials.compare_count'], 'App\Http\ViewComposers\CompareCountComposer');
        /*View::composer(
            ['partials.cart_header', 'partials.popups', 'profile.partials.cart'],
            'App\Http\ViewComposers\CartHeaderComposer'
        );*/
        //View::composer('partials.cart_filter', 'App\Http\ViewComposers\CartFiltersComposer');

        View::composer('category.partials.select_filter', 'App\Http\ViewComposers\FilterSelectComposer');
        //View::composer('product.partials.interesting_products', 'App\Http\ViewComposers\InterestingProductsComposer');

        //View::composer('partials.header_phones_email', 'App\Http\ViewComposers\HeaderPhonesEmailComposer');

        /*View::composer(
            ['partials.share_social', 'product.partials.share_social'],
            'App\Http\ViewComposers\ShareSocialComposer'
        );*/
        //View::composer('product.partials.group_join', 'App\Http\ViewComposers\ProductGroupJoinComposer');

        //View::composer('universal.blocks.banner_slider', 'App\Http\ViewComposers\Universal\BannerSliderComposer');
        //View::composer('universal.blocks.client_logo', 'App\Http\ViewComposers\Universal\ClientLogoComposer');
        View::composer(
            'product.blocks.best_offers_product',
            'App\Http\ViewComposers\Universal\BestOffersComposer'
        );
        /*View::composer(
            'universal.blocks.products_hit_slider',
            'App\Http\ViewComposers\Universal\ProductHitSliderComposer'
        );*/
        /*View::composer(
            'universal.blocks.products_hit_auto_slider',
            'App\Http\ViewComposers\Universal\ProductHitAutoSliderComposer'
        );*/

        /*View::composer(
            'universal.blocks.products_popular_slider',
            'App\Http\ViewComposers\Universal\ProductPopularSliderComposer'
        );*/
        /*View::composer(
            'universal.blocks.categories_popular_slider',
            'App\Http\ViewComposers\Universal\CategoryPopularSliderComposer'
        );*/
        /*View::composer(
            'universal.blocks.products_sale_slider',
            'App\Http\ViewComposers\Universal\ProductSaleSliderComposer'
        );*/

        //View::composer('universal.blocks.instagram', 'App\Http\ViewComposers\Universal\InstagramFeedComposer');
        //View::composer('product.partials.relation_products', 'App\Http\ViewComposers\RelationProductsComposer');
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        //
    }
}
