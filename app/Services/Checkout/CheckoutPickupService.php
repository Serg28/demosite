<?php

namespace App\Services\Checkout;

use App\Models\DeliveryPickupPoint;
use Illuminate\Support\Collection;

class CheckoutPickupService
{
    public function forDelivery(int $deliveryId, ?int $cityId = null): Collection
    {
        if ($deliveryId < 1) {
            return collect();
        }

        return DeliveryPickupPoint::query()
            ->active()
            ->where('delivery_id', $deliveryId)
            ->when($cityId > 0, fn ($q) => $q->where('city_id', $cityId))
            ->get()
            ->map(fn (DeliveryPickupPoint $p) => [
                'id'      => $p->id,
                'title'   => $p->t('title'),
                'address' => $p->address,
            ])
            ->values();
    }
}
