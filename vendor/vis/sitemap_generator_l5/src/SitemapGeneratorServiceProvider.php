<?php

namespace Vis\SitemapGenerator;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class SitemapGeneratorServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        require __DIR__ . '/../vendor/autoload.php';

        $this->setupRoutes($this->app->router);

        $this->loadViewsFrom(realpath(__DIR__ . '/resources/views'), 'sitemap');

        $this->publishes([
            __DIR__ . '/config' => config_path('sitemap-generator/')
        ], 'sitemap-generator-config');

    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        if (!$this->app->routesAreCached()) {
            require __DIR__ . '/Http/Routes/routes.php';
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     *
     */
    public function provides()
    {
    }

}
