<?php

namespace App\Actions\Checkout;

use App\Models\DiscountCard;

class ApplyDiscountCard
{
    public function handle(string $code, float $subtotal): array
    {
        $card = DiscountCard::query()
            ->where('code', $code)
            ->where('is_active', 1)
            ->first();

        if (! $card) {
            return ['success' => false, 'message' => __t('Дисконтну картку не знайдено')];
        }

        return [
            'success' => true,
            'card' => $card,
            'discount' => $card->getAmount($subtotal),
        ];
    }
}
