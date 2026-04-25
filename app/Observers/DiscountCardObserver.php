<?php

namespace App\Observers;

use App\Models\DiscountCard;

class DiscountCardObserver
{
    public function updated(DiscountCard $discountCard)
    {
        $discountCard->updateQuietly([
            'waiting_1c' => 1
        ]);
    }

    public function created(DiscountCard $discountCard)
    {
        $discountCard->updateQuietly([
            'waiting_1c' => 1
        ]);
    }
}
