<?php

namespace App\Contracts;

use App\Models\Delivery;
use App\Models\Order;

interface DeliveryServiceInterface
{
    public function calculatePrice(Delivery $delivery, Order $order): float;

    public function isFree(Delivery $delivery, float $orderTotal): bool;
}
