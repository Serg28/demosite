<?php

namespace App\Providers;

use App\Models\Comment;
//use App\Models\DiscountCard;
use App\Models\Order;
use App\Models\OrderProducts;
use App\Models\Product;
use App\Observers\CommentObserver;
//use App\Observers\DiscountCardObserver;
use App\Observers\OrderObserver;
use App\Observers\OrderProductsObserver;
use App\Observers\ProductObserver;
use App\Traits\PerformanceMonitoringTrait;
use Carbon\Carbon;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
//use Dedoc\Scramble\Scramble;
//use Dedoc\Scramble\Support\Generator\OpenApi;
//use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
//use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    use PerformanceMonitoringTrait;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->bindSearchClient();
    }

    public function boot(): void
    {
        Carbon::setLocale(
            config('app.locale') === 'ua' ? 'uk' : config('app.locale')
        );

        config(['mail.from.address' => setting('mail_from_address') ?? '']);
        config(['mail.from.name' => setting('mail_from_name') ?? '']);

        //\App::singleton('user', function () {
        //    return Sentinel::check();
        //});

        \App::singleton('user', function () {
            try {
                return Sentinel::check();
            } catch (NotActivatedException $e) {
                return null;
            }
        });

        Paginator::useBootstrap();

        Product::observe(ProductObserver::class);
        Order::observe(OrderObserver::class);
        OrderProducts::observe(OrderProductsObserver::class);
        Comment::observe(CommentObserver::class);
        //DiscountCard::observe(DiscountCardObserver::class);

        Relation::morphMap([
            'news' => 'App\Models\News',
            'product' => 'App\Models\Product',
            'category' => 'App\Models\Category'
        ]);

        //Model::preventLazyLoading(! app()->isProduction());

        //-------------------------------------------------------------------

        /*Builder::macro('whereNotNullOrEmpty', function ($field) {
            return $this->where(function ($query) use ($field) {
                return $query->where($field, '<>', null)->where($field, '<>', '');
            });
        });*/
    }

    private function bindSearchClient(): void
    {
        $this->app->bind(Client::class, function () {
            return ClientBuilder::create()
                ->setHosts(config('services.search.hosts'))
                ->build();
        });
    }
}
