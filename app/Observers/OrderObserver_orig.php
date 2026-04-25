<?php

namespace App\Observers;

use App\Models\CumulativeDiscount;
use App\Models\Order;

class OrderObserver_orig
{
    public function updated(Order $order): void
    {
        if ($order->user) {
            $cost = $order->user->orders()->where('order_status_id', '3')->sum('cost');

            $discount = $this->getDiscount($cost);

            $order->user->discount_cumulative = $discount;
            $order->user->save();
        }
    }

    private function getDiscount(float $costAll): float
    {
        $discounts = CumulativeDiscount::orderBy('discount', 'desc')->pluck('discount', 'total_sum')->toArray();

        foreach ($discounts as $cost => $discount) {
            if ($costAll > $cost) {
                return $discount;
            }
        }

        return 0;
    }
}
