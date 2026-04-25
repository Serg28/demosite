<?php
namespace Litvin\Redirectmap;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider as Provider;
use App\Exceptions\NotFoundException;

/**
 * Service provider for the exception handler.
 *
 */
class ServiceProvider extends Provider
{
    /**
     * Add an alias for the exception handler facade.
     *
     * @return    void
     */
    public function boot()
    {
        require __DIR__.'/../vendor/autoload.php';

        $this->publishes([
            __DIR__.'/migrations/' => database_path('/migrations')
        ], 'redirect_map');

        $this->publishes([
            __DIR__ . '/config/' => config_path('/builder/tb-definitions')
        ], 'redirect_map');

        $this->loadViewsFrom(realpath(__DIR__.'/resources/views'), 'redirect');

        require __DIR__.'/Http/router.php';
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->extend(ExceptionHandler::class, function ($handler, $app) {
            return new Decorator($handler);
        });
    }

}