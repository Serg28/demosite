<?php

namespace App\Services\Checkout;

use App\Models\Delivery;
use App\Models\PayMethod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CheckoutPaymentService
{
    private const TTL = 3600;

    public function forDelivery(int $deliveryId): Collection
    {
        return Cache::remember("checkout.payments.delivery.{$deliveryId}", self::TTL, function () use ($deliveryId) {
            $delivery = Delivery::query()->active()->find($deliveryId);

            if ($delivery === null) {
                return collect();
            }

            return $delivery->payments()
                ->active()
                ->get()
                ->map(fn (PayMethod $method) => [
                    'id'                 => $method->id,
                    'title'              => $method->t('title'),
                    'slug'               => $method->slug,
                    'gateway'            => $method->gateway,
                    'commission_percent' => $method->commission_percent,
                    'use_hold'           => $method->use_hold,
                ])
                ->values();
        });
    }
}
