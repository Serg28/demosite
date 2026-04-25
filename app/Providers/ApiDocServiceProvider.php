<?php

namespace App\Providers;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ApiDocServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
    }

    public function boot(): void
    {
        if (!app()->isProduction()) {
            //Шлюз для доступа к просмотру документации к API в средах, кроме local
            Gate::define('viewApiDocs', static function ($user = null) {
                $user = $user ?: Sentinel::check();
                return $user && in_array($user->email, array_map('trim', explode(',', setting('site-api-docs-allowed-emails'))));
            });

            //Шлюз для аутентификации в API-документации
            Scramble::extendOpenApi(static function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer', setting('site-api-bearer-key') ?: config('services.api.token'))
                );
            });
        }
    }
}
