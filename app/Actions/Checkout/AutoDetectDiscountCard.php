<?php

namespace App\Actions\Checkout;

use App\Models\DiscountCard;

class AutoDetectDiscountCard
{
    public function handle(string $phone): ?DiscountCard
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($clean) < 9) {
            return null;
        }

        $last9 = substr($clean, -9);

        return DiscountCard::query()
            ->where('is_active', true)
            ->where(function ($q) use ($clean, $last9) {
                $q->where('phone', $clean)
                    ->orWhere('phone', '+'.$clean)
                    ->orWhere('phone', '380'.$last9)
                    ->orWhere('phone', '+380'.$last9)
                    ->orWhere('phone', '0'.$last9);
            })
            ->first();
    }
}
