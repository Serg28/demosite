<?php

namespace App\Services\Delivery;

use App\DTO\Checkout\CheckoutContext;
use App\Models\Delivery;

class DeliveryService
{
    public function isFree(Delivery $delivery, float $orderTotal): bool
    {
        $freeCost = $delivery->free_cost;

        return $freeCost !== null
            && $freeCost > 0
            && $orderTotal >= $freeCost;
    }

    public function calculatePrice(Delivery $delivery, CheckoutContext $context): float
    {
        // Базова ціна з моделі; в майбутньому ShippingCalculator (Phase 6)
        return (float) ($delivery->price ?? 0.0);
    }

    /**
     * Повертає активні служби доставки, сумісні з обраним методом оплати.
     */
    public function available(int $payMethodId): \Illuminate\Database\Eloquent\Collection
    {
        return Delivery::query()
            ->where('is_active', 1)
            ->whereHas('payments', fn ($q) => $q->where('pay_methods.id', $payMethodId))
            ->orderBy('priority')
            ->get();
    }
}
