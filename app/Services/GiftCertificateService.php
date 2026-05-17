<?php

namespace App\Services;

use App\Models\GiftCertificate;
use App\Models\Order;
use Illuminate\Support\Collection;

class GiftCertificateService
{
    private function cacheKey(string $code): string
    {
        return "gift:reserve:{$code}";
    }

    public function findValid(string $code): ?GiftCertificate
    {
        return GiftCertificate::query()
            ->valid()
            ->where('code', $code)
            ->first();
    }

    /**
     * Додати код сертифіката до сесії.
     * Повертає false, якщо вже застосовано, недійсний або зарезервований іншим.
     */
    public function addCode(string $code): bool
    {
        $applied = session('gift_certificates', []);

        if (in_array($code, $applied, true)) {
            return false;
        }

        if ($this->findValid($code) === null) {
            return false;
        }

        if ($this->isReserved($code)) {
            return false;
        }

        $this->reserve($code);
        $applied[] = $code;
        session(['gift_certificates' => $applied]);

        return true;
    }

    /**
     * Видалити код сертифіката з сесії та звільнити резерв.
     */
    public function removeCode(string $code): void
    {
        $applied = session('gift_certificates', []);
        $applied = array_values(array_filter($applied, fn (string $c) => $c !== $code));
        session(['gift_certificates' => $applied]);
        cache()->forget($this->cacheKey($code));
    }

    /**
     * Повернути застосовані сертифікати для відображення.
     * $subtotal — зарезервовано для Phase 4.11 (часткове покриття).
     *
     * @return Collection<int, array{id: int, code: string, amount: float, expiry_date: string|null}>
     */
    public function getAppliedCertificates(float $subtotal): Collection
    {
        $codes = session('gift_certificates', []);

        if (empty($codes)) {
            return collect();
        }

        return GiftCertificate::query()
            ->whereIn('code', $codes)
            ->get()
            ->map(fn (GiftCertificate $cert) => [
                'id'          => $cert->id,
                'code'        => $cert->code,
                'amount'      => $cert->amount,
                'expiry_date' => $cert->expires_at?->format('d.m.Y'),
            ]);
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
