<?php

namespace App\Services\Sms\Drivers;

use App\Contracts\SmsProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class VodafoneDriver implements SmsProvider
{
    public function __construct(private readonly array $config) {}

    public function send(string $phone, string $message): void
    {
        $response = Http::withToken($this->config['token'])
            ->post('https://gateway.vodafone.ua/b2c/sms/api/1/messages', [
                'from'    => $this->config['sender'],
                'to'      => $phone,
                'content' => $message,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Vodafone SMS error: '.$response->body());
        }
    }
}
