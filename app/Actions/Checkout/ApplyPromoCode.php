<?php

namespace App\Actions\Checkout;

use App\Models\PromoCode;

class ApplyPromoCode
{
    public function handle(string $code, float $subtotal): array
    {
        $promo = PromoCode::query()
            ->where('code', $code)
            ->where('is_active', 1)
            ->first();

        if (! $promo) {
            return ['success' => false, 'message' => __t('Промокод не знайдено')];
        }

        if ($promo->expires_at && $promo->expires_at->isPast()) {
            return ['success' => false, 'message' => __t('Термін дії промокоду закінчився')];
        }

        if ($promo->usage_type === 'once' && $promo->is_used) {
            return ['success' => false, 'message' => __t('Цей промокод вже використано')];
        }

        if ($promo->max_uses !== null && $promo->used_count >= $promo->max_uses) {
            return ['success' => false, 'message' => __t('Ліміт використань цього промокоду вичерпано')];
        }

        if ($promo->min_order_amount && $subtotal < $promo->min_order_amount) {
            return [
                'success' => false,
                'message' => __t('Мінімальна сума замовлення для цього промокоду: :amount грн', [
                    'amount' => number_format(
                        $promo->min_order_amount,
                        config('cart.format.decimals', 0),
                        config('cart.format.decimal_point', '.'),
                        config('cart.format.thousand_separator', ' '),
                    ),
                ]),
            ];
        }

        return [
            'success' => true,
            'promo' => $promo,
            'discount' => $promo->getAmount($subtotal),
        ];
    }
}
