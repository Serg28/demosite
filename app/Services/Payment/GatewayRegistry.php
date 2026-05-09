<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

class GatewayRegistry
{
    public function resolve(string $slug): PaymentGatewayInterface
    {
        $gateways = config('payment.gateways', []);

        if (! isset($gateways[$slug])) {
            throw new InvalidArgumentException("Payment gateway [{$slug}] not registered.");
        }

        return app($gateways[$slug]);
    }

    public function has(string $slug): bool
    {
        return isset(config('payment.gateways', [])[$slug]);
    }
}
