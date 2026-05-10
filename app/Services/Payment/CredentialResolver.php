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
                $query->forDay($dayOfWeek)->orWhere('is_default', 1);
            })
            ->orderByRaw('CASE WHEN JSON_CONTAINS(days_of_week, ?) THEN 0 ELSE 1 END', [(string) $dayOfWeek])
            ->first();

        return $credential?->credentials ?? [];
    }
}
