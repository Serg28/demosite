<?php

namespace App\Services\Sms\Drivers;

use App\Contracts\SmsProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AlphaNameDriver implements SmsProvider
{
    public function __construct(private readonly array $config) {}

    public function send(string $phone, string $message): void
    {
        $response = Http::post('https://alphasms.ua/api/json.php', [
            'auth'  => ['login' => $this->config['login'], 'password' => $this->config['password']],
            'data'  => [['type' => 'sms', 'from' => $this->config['sender'], 'to' => $phone, 'text' => $message]],
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('AlphaName SMS error: '.$response->body());
        }
    }
}
