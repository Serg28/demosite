<?php

namespace App\Services\Checkout;

use App\Models\DeliveryWarehouse;
use Illuminate\Support\Collection;

class CheckoutWarehouseService
{
    private const DEFAULT_LIMIT = 20;

    public function search(string $deliverySlug, int $cityId, string $q, int $limit = self::DEFAULT_LIMIT): Collection
    {
        $q = trim($q);

        if (mb_strlen($q) < 1 || ! $deliverySlug || ! $cityId) {
            return collect();
        }

        if (! DeliveryWarehouse::hasWarehouseSearch($deliverySlug)) {
            return collect();
        }

        return DeliveryWarehouse::query()
            ->active()
            ->forDeliverySlug($deliverySlug)
            ->forCity($cityId)
            ->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(title, '$.ua')) LIKE ?",
                ['%'.$q.'%'],
            )
            ->limit($limit)
            ->get()
            ->map(fn (DeliveryWarehouse $w) => ['id' => $w->id, 'title' => $w->t('title')])
            ->values();
    }
}
