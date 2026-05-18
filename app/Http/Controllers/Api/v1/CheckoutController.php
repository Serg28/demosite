<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\Checkout\CheckoutCityService;
use App\Services\Checkout\CheckoutDeliveryService;
use App\Services\Checkout\CheckoutPaymentService;
use App\Services\Checkout\CheckoutPickupService;
use App\Services\Checkout\CheckoutWarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutCityService $cityService,
        private readonly CheckoutWarehouseService $warehouseService,
        private readonly CheckoutDeliveryService $deliveryService,
        private readonly CheckoutPaymentService $paymentService,
        private readonly CheckoutPickupService $pickupService,
    ) {}

    public function searchCities(Request $request): JsonResponse
    {
        return response()->json(
            $this->cityService->search((string) $request->input('q', ''))
        );
    }

    public function searchWarehouses(Request $request): JsonResponse
    {
        return response()->json(
            $this->warehouseService->search(
                (string) $request->input('delivery_slug', ''),
                (int) $request->input('city_id', 0),
                (string) $request->input('q', ''),
            )
        );
    }

    public function deliveriesForCity(Request $request): JsonResponse
    {
        $cityId = (int) $request->input('city_id', 0);

        if ($cityId < 1) {
            return response()->json([]);
        }

        return response()->json(
            $this->deliveryService->forCity($cityId)
        );
    }

    public function paymentsForDelivery(Request $request): JsonResponse
    {
        $deliveryId = (int) $request->input('delivery_id', 0);

        if ($deliveryId < 1) {
            return response()->json([]);
        }

        return response()->json(
            $this->paymentService->forDelivery($deliveryId)
        );
    }

    public function pickupPoints(Request $request): JsonResponse
    {
        $deliveryId = (int) $request->input('delivery_id', 0);
        $cityId     = (int) $request->input('city_id', 0);

        return response()->json(
            $this->pickupService->forDelivery($deliveryId, $cityId ?: null)
        );
    }
}
