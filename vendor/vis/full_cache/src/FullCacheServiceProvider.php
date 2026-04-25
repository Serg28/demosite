<?php

namespace Vis\FullCache;

use Illuminate\Support\ServiceProvider;

class FullCacheServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/full_cache.php' => config_path('full_cache.php'),
        ], 'config_full_cache');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/full_cache.php', 'full_cache');
    }
}
