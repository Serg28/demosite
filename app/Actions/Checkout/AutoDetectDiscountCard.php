<?php

namespace App\Actions\Checkout;

use App\Models\DiscountCard;
use Illuminate\Support\Facades\Cache;

class AutoDetectDiscountCard
{
    private const CACHE_TTL = 300; // 5 min

    public function handle(string $phone): ?DiscountCard
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($clean) < 9) {
            return null;
        }

        $last9 = substr($clean, -9);

        $cardId = Cache::remember(
            "discount_card.phone.{$clean}",
            self::CACHE_TTL,
            fn (): int => (int) DiscountCard::query()
                ->where('is_active', true)
                ->where(function ($q) use ($clean, $last9): void {
                    $q->where('phone', $clean)
                        ->orWhere('phone', '+'.$clean)
                        ->orWhere('phone', '380'.$last9)
                        ->orWhere('phone', '+380'.$last9)
                        ->orWhere('phone', '0'.$last9);
                })
                ->value('id')
        );

        return $cardId ? DiscountCard::find($cardId) : null;
    }
}
