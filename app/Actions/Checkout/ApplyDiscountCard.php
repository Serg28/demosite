<?php

namespace App\Actions\Checkout;

use App\Models\DiscountCard;

class ApplyDiscountCard
{
    public function handle(string $code, float $subtotal): array
    {
        $card = DiscountCard::query()
            ->where('is_active', 1)
            ->where(function ($query) use ($code): void {
                $query->where('code', $code)
                    ->orWhere('barcode', $code)
                    ->orWhere('phone', $this->normalizePhone($code))
                    ->orWhere('phone', '+' . $this->normalizePhone($code));
            })
            ->first();

        if (! $card) {
            return ['success' => false, 'message' => __t('Дисконтну картку не знайдено')];
        }

        return [
            'success'  => true,
            'card'     => $card,
            'discount' => $card->getAmount($subtotal),
        ];
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        // 380XXXXXXXXX → 380XXXXXXXXX, 0XXXXXXXXX → 380XXXXXXXXX
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $digits = '38' . $digits;
        }

        return $digits;
    }
}
