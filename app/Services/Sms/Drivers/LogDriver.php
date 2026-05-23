<?php

namespace App\Services\Sms\Drivers;

use App\Contracts\SmsProvider;
use Illuminate\Support\Facades\Log;

class LogDriver implements SmsProvider
{
    public function send(string $phone, string $message): void
    {
        Log::channel('sms')->info('SMS', ['phone' => $phone, 'message' => $message]);
    }
}
