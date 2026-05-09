<?php

namespace App\Services\Payment;

use App\Models\PaymentCredential;

class CredentialResolver
{
    /**
     * Повертає credentials для платіжного методу за поточним днем тижня.
     * Fallback → is_default = 1.
     *
     * @return array<string, mixed>
     */
    public function resolve(int $payMethodId): array
    {
        $dayOfWeek = now()->isoWeekday(); // 1=Пн ... 7=Нд

        $credential = PaymentCredential::query()
            ->where('pay_method_id', $payMethodId)
            ->where(function ($query) use ($dayOfWeek): void {
                $query->where('day_of_week', $dayOfWeek)
                    ->orWhere('is_default', 1);
            })
            ->orderByRaw('CASE WHEN day_of_week = ? THEN 0 ELSE 1 END', [$dayOfWeek])
            ->first();

        return $credential?->credentials ?? [];
    }
}
