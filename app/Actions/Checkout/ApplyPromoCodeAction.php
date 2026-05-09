<?php

namespace App\Actions\Checkout;

use App\Models\PromoCode;

class ApplyPromoCodeAction
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

        if ($promo->min_order_amount && $subtotal < $promo->min_order_amount) {
            return [
                'success' => false,
                'message' => __t('Мінімальна сума замовлення для цього промокоду: :amount грн', [
                    'amount' => number_format($promo->min_order_amount, 0, '.', ' '),
                ]),
            ];
        }

        return [
            'success'   => true,
            'promo'     => $promo,
            'discount'  => $promo->getAmount($subtotal),
        ];
    }
}
