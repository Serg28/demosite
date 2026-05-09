<?php

namespace App\Contracts;

use App\Models\Order;

interface Holdable
{
    public function captureHold(Order $order): bool;

    public function releaseHold(Order $order): bool;
}
