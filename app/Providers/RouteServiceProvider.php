<?php

namespace App\Providers;

use App\Models\Characteristic;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define the routes for the application.
     */
    public function boot(): void
    {
        //$slugsCharacteristic = Characteristic::select('slug')->rememberForever()->cacheTags('characteristics')->pluck('slug')->toArray();

        Route::pattern('id', '[0-9]+');
        Route::pattern('slug', '[a-z0-9-]+');
        Route::pattern('product', '[0-9]+');
        Route::pattern('order', '[0-9]+');
        //Route::pattern('characteristic', implode('|', $slugsCharacteristic));
    }

    public function map(): void
    {
        $this->mapApiRoutes();

        $this->mapAdminRoutes();

        //$this->mapFrontRoutes();

        $this->mapWebRoutes();
    }

    protected function mapFrontRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/front.php'));

        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/front/shop3.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    protected function mapAdminRoutes(): void
    {
        Route::prefix('admin')
            ->middleware(['web', 'auth.admin'])
            ->namespace('App\Http\Controllers\Admin')
            ->group(base_path('routes/admin.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    //protected function configureRateLimiting()
    //{
    //    RateLimiter::for('api', function (Request $request) {
    //        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    //    });
    //}
}
