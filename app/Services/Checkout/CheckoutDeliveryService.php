<?php

namespace App\Services\Checkout;

use App\Models\Delivery;
use Illuminate\Support\Collection;

class CheckoutDeliveryService
{
    public function forCity(int $cityId): Collection
    {
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
    }
}
