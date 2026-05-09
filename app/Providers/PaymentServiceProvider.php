<?php

namespace App\Providers;

use App\Services\Payment\CommissionCalculator;
use App\Services\Payment\CredentialResolver;
use App\Services\Payment\GatewayRegistry;
use App\Services\Payment\PaymentInvoiceService;
use App\Services\Payment\Strategies\FlatCommissionStrategy;
use App\Services\Payment\Strategies\MonoPartsCommissionStrategy;
use App\Services\Payment\Strategies\PrivatPPCommissionStrategy;
use App\Services\Payment\WebhookProcessor;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GatewayRegistry::class);
        $this->app->singleton(CredentialResolver::class);
        $this->app->singleton(CommissionCalculator::class);
        $this->app->singleton(PaymentInvoiceService::class);
        $this->app->singleton(WebhookProcessor::class);

        $this->app->bind(FlatCommissionStrategy::class);
        $this->app->bind(MonoPartsCommissionStrategy::class);
        $this->app->bind(PrivatPPCommissionStrategy::class);
    }

    public function boot(): void {}
}
