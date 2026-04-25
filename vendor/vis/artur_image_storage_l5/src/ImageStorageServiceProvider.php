<?php namespace Vis\ImageStorage;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class ImageStorageServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(realpath(__DIR__ . '/resources/views'), 'image-storage');

        $this->publishes([
            __DIR__ . '/published' => public_path('packages/vis/image-storage'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/published' => public_path('packages/vis/image-storage'),
        ], 'image-storage-public');

        $this->publishes([
            __DIR__ . '/config' => config_path('image-storage/')
        ], 'image-storage-config');

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
            require __DIR__ . '/Http/Routers/routers.php';
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

    public function provides()
    {
    }
}



