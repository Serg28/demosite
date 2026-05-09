<?php

namespace App\Providers;

use App\Adapters\Delivery\JustinAdapter;
use App\Adapters\Delivery\MeestAdapter;
use App\Adapters\Delivery\NovaPoshtaAdapter;
use App\Adapters\Delivery\NullTranslatorAdapter;
use App\Adapters\Delivery\RozetkaAdapter;
use App\Adapters\Delivery\UkrposhtaAdapter;
use App\Contracts\TranslatorAdapter;
use App\Services\Delivery\CarrierAdapterRegistry;
use App\Services\Delivery\DeliveryService;
use Illuminate\Support\ServiceProvider;

class DeliveryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DeliveryService::class);

        $this->app->bind(TranslatorAdapter::class, NullTranslatorAdapter::class);

        $this->app->singleton(CarrierAdapterRegistry::class, function ($app) {
            $registry = new CarrierAdapterRegistry();

            $registry->register($app->make(NovaPoshtaAdapter::class));
            $registry->register($app->make(UkrposhtaAdapter::class));
            $registry->register($app->make(JustinAdapter::class));
            $registry->register($app->make(MeestAdapter::class));
            $registry->register($app->make(RozetkaAdapter::class));

            return $registry;
        });
    }

    public function boot(): void {}
}
