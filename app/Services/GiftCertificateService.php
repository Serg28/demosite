<?php

namespace App\Services;

use App\Models\GiftCertificate;
use App\Models\Order;

class GiftCertificateService
{
    private function cacheKey(string $code): string
    {
        return "gift:reserve:{$code}";
    }

    public function findValid(string $code): ?GiftCertificate
    {
        return GiftCertificate::query()
            ->where('code', $code)
            ->where('is_active', 1)
            ->where('is_used', 0)
            ->first();
    }

    public function reserve(string $code): void
    {
        $ttl = config('checkout.gift_certificate_reserve_ttl', 3600);
        cache()->put($this->cacheKey($code), true, $ttl);
    }

    public function isReserved(string $code): bool
    {
        return (bool) cache()->get($this->cacheKey($code));
    }

    /**
     * Списати сертифікати після успішного замовлення.
     *
     * @param  string[]  $codes
     */
    public function finalize(Order $order, array $codes): void
    {
        foreach ($codes as $code) {
            $certificate = $this->findValidIncludingReserved($code);

            if (! $certificate) {
                continue;
            }

            $certificate->update(['is_used' => 1, 'used_in_order_id' => $order->id]);
            cache()->forget($this->cacheKey($code));
        }
    }

    /**
     * Повернути сертифікати при відміні замовлення.
     */
    public function refund(Order $order): void
    {
        GiftCertificate::query()
            ->where('used_in_order_id', $order->id)
            ->update(['is_used' => 0, 'used_in_order_id' => null]);
    }

    private function findValidIncludingReserved(string $code): ?GiftCertificate
    {
        return GiftCertificate::query()
            ->where('code', $code)
            ->where('is_active', 1)
            ->first();
    }
}
