<?php

namespace App\Actions\Auth;

use App\Contracts\SmsProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendOtpCode
{
    public function handle(string $identifier, string $channel = 'sms'): void
    {
        $code = (string) random_int(100000, 999999);
        Cache::put("otp:{$identifier}", $code, now()->addMinutes(10));

        if ($channel === 'sms') {
            app(SmsProvider::class)->send($identifier, __t('Код підтвердження: ').$code);
        } else {
            Mail::raw(__t('Ваш код підтвердження: ').$code, function ($mail) use ($identifier) {
                $mail->to($identifier)->subject(__t('Код для входу'));
            });
        }
    }
}
