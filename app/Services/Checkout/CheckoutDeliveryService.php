<?php

namespace App\Services\Checkout;

use App\Models\Delivery;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CheckoutDeliveryService
{
    private const TTL = 3600;

    public function forCity(int $cityId): Collection
    {
        return Cache::remember("checkout.deliveries.city.{$cityId}", self::TTL, function () use ($cityId) {
            return Delivery::query()
                ->active()
                ->forCity($cityId)
                ->get()
                ->map(fn (Delivery $delivery) => [
                    'id'          => $delivery->id,
                    'title'       => $delivery->t('title'),
                    'slug'        => $delivery->slug,
                    'price'       => $delivery->price,
                    'free_from'   => $delivery->free_cost,
                    'description' => $delivery->t('description'),
                ])
                ->values();
        });
    }
}
