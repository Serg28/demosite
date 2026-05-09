<?php

namespace App\Contracts;

use App\Models\Order;

interface Returnable
{
    public function return(Order $order): bool;
}
