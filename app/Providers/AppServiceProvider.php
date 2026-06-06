<?php

namespace App\Providers;

use App\Contracts\SmsProvider;
use App\Models\User;
use App\Services\CompareService;
use App\Services\CurrencyService;
use App\Services\Sms\SmsManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CurrencyService::class);

        $this->app->singleton('sms', fn ($app) => new SmsManager($app));
        $this->app->bind(SmsProvider::class, fn ($app) => $app['sms']->driver());

        // Octane-safe: scoped = reset per request (не singleton!)
        $this->app->scoped('user', static fn (): ?User => Auth::user());
        $this->app->scoped(CompareService::class);
    }

    public function boot(): void
    {
        // View composers → ViewServiceProvider
        // OrderObserver → реєструється через #[ObservedBy] атрибут на моделі
    }
}
