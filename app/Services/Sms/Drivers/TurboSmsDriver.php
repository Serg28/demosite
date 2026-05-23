<?php

namespace App\Services\Sms\Drivers;

use App\Contracts\SmsProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TurboSmsDriver implements SmsProvider
{
    public function __construct(private readonly array $config) {}

    public function send(string $phone, string $message): void
    {
        $response = Http::withToken($this->config['api_key'])
            ->post('https://api.turbosms.ua/message/send.json', [
                'recipients' => [$phone],
                'sms'        => ['sender' => $this->config['sender'], 'text' => $message],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('TurboSMS error: '.$response->body());
        }
    }
}
