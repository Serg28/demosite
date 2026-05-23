<?php

namespace App\Services\Sms;

use App\Contracts\SmsProvider;
use App\Services\Sms\Drivers\AlphaNameDriver;
use App\Services\Sms\Drivers\LogDriver;
use App\Services\Sms\Drivers\TurboSmsDriver;
use App\Services\Sms\Drivers\VodafoneDriver;
use Illuminate\Support\Manager;

class SmsManager extends Manager implements SmsProvider
{
    public function send(string $phone, string $message): void
    {
        $this->driver()->send($phone, $message);
    }

    public function getDefaultDriver(): string
    {
        return config('sms.default', 'log');
    }

    protected function createLogDriver(): LogDriver
    {
        return new LogDriver();
    }

    protected function createTurbosmsDriver(): TurboSmsDriver
    {
        return new TurboSmsDriver(config('sms.drivers.turbosms', []));
    }

    protected function createVodafoneDriver(): VodafoneDriver
    {
        return new VodafoneDriver(config('sms.drivers.vodafone', []));
    }

    protected function createAlphanameDriver(): AlphaNameDriver
    {
        return new AlphaNameDriver(config('sms.drivers.alphaname', []));
    }
}
