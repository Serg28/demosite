<?php

namespace App\Providers;

use App\Services\Delivery\DeliveryService;
use Illuminate\Support\ServiceProvider;

class DeliveryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DeliveryService::class);
    }

    public function boot(): void {}
}
