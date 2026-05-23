<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Cache;

class VerifyOtpCode
{
    public function handle(string $identifier, string $code): bool
    {
        $stored = Cache::get("otp:{$identifier}");

        if ((string) $stored !== $code) {
            return false;
        }

        Cache::forget("otp:{$identifier}");

        return true;
    }
}
