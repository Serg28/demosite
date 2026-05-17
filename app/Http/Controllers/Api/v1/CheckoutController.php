<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\DeliveryWarehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function searchCities(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $cities = City::query()
            ->active()
            ->where(function ($query) use ($q) {
                $query->where('title->ua', 'like', $q.'%')
                    ->orWhere('title->ru', 'like', $q.'%');
            })
            ->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.ua')) ASC")
            ->limit(15)
            ->get();

        return response()->json(
            $cities->map(fn ($c) => ['id' => $c->id, 'title' => $c->t('title')])->values()
        );
    }

    public function searchWarehouses(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));
        $deliverySlug = $request->input('delivery_slug', '');
        $cityId = (int) $request->input('city_id', 0);

        if (mb_strlen($q) < 1 || ! $deliverySlug || ! $cityId) {
            return response()->json([]);
        }

        if (! DeliveryWarehouse::hasWarehouseSearch($deliverySlug)) {
            return response()->json([]);
        }

        $warehouses = DeliveryWarehouse::query()
            ->active()
            ->forDeliverySlug($deliverySlug)
            ->forCity($cityId)
            ->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(title, '$.ua')) LIKE ?",
                ['%'.$q.'%'],
            )
            ->limit(20)
            ->get();

        return response()->json(
            $warehouses->map(fn ($w) => ['id' => $w->id, 'title' => $w->t('title')])->values()
        );
    }
}
