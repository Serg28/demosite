<?php

namespace App\Services\Payment\Gateways\LiqPay;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LiqPayClient
{
    private const CHECKOUT_URL = 'https://www.liqpay.ua/api/3/checkout';

    private const API_URL = 'https://www.liqpay.ua/api/request';

    public function __construct(
        private readonly string $publicKey,
        private readonly string $privateKey,
    ) {}

    /**
     * Будує дані форми для LiqPay Checkout.
     *
     * @param  array<string, mixed>  $params
     * @return array{data: string, signature: string, checkout_url: string}
     */
    public function buildFormData(array $params): array
    {
        $params = array_merge($params, [
            'version'    => '3',
            'public_key' => $this->publicKey,
        ]);

        $data      = base64_encode((string) json_encode($params));
        $signature = $this->sign($data);

        return [
            'data'         => $data,
            'signature'    => $signature,
            'checkout_url' => self::CHECKOUT_URL,
        ];
    }

    /**
     * Перевіряє підпис webhook-payload від LiqPay.
     */
    public function verifySignature(string $data, string $signature): bool
    {
        return hash_equals($this->sign($data), $signature);
    }

    /**
     * Декодує base64+json поле data з webhook/response.
     *
     * @return array<string, mixed>
     */
    public function decodeData(string $data): array
    {
        return json_decode(base64_decode($data), true) ?? [];
    }

    /**
     * Виконує запит до LiqPay Server API.
     *
     * @param  array<string, mixed>  $params
     */
    public function api(string $action, array $params): ?object
    {
        $params = array_merge($params, [
            'action'     => $action,
            'version'    => '3',
            'public_key' => $this->publicKey,
        ]);

        $data      = base64_encode((string) json_encode($params));
        $signature = $this->sign($data);

        $response = Http::asForm()
            ->timeout(10)
            ->post(self::API_URL, compact('data', 'signature'));

        if (! $response->successful()) {
            Log::warning('LiqPay API error', [
                'action' => $action,
                'status' => $response->status(),
            ]);

            return null;
        }

        return $response->object();
    }

    private function sign(string $data): string
    {
        return base64_encode(sha1($this->privateKey . $data . $this->privateKey, true));
    }
}
