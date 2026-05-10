<?php

namespace App\Services\Payment\Gateways\Platon;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlatonClient
{
    private const PAYMENT_CODE = 'CC';

    public function __construct(
        private readonly string $key,
        private readonly string $password,
        private readonly string $apiUrl,
    ) {}

    /**
     * Будує POST-дані для редіректу на Platon.
     * Endpoint: POST {apiUrl}/payment/auth
     *
     * @param  array<string, mixed>  $params  order, amount, description, url, email, phone
     * @return array<string, mixed>
     */
    public function buildFormData(array $params): array
    {
        $data = $this->generateData((float) $params['amount'], (string) $params['description']);
        $callbackUrl = (string) $params['url'];

        return [
            'key' => $this->key,
            'payment' => self::PAYMENT_CODE,
            'data' => $data,
            'url' => $callbackUrl,
            'email' => $params['email'] ?? '',
            'phone' => $params['phone'] ?? '',
            'order' => (string) $params['order'],
            'sign' => $this->signForm($data, $callbackUrl),
            'checkout_url' => $this->apiUrl.'/payment/auth',
        ];
    }

    /**
     * Перевіряє статус транзакції через Platon API.
     */
    public function checkStatus(string $orderId): ?object
    {
        $response = Http::timeout(10)->post($this->apiUrl.'/payment/status', [
            'key' => $this->key,
            'order_id' => $orderId,
            'sign' => $this->signStatus($orderId),
        ]);

        if (! $response->successful()) {
            Log::channel('payments')->warning('Platon: checkStatus error', [
                'orderId' => $orderId,
                'status' => $response->status(),
            ]);

            return null;
        }

        return $response->object();
    }

    /**
     * Верифікує підпис callback від Platon.
     * sign = MD5(strtoupper(strrev(email) + password + order + strrev(card_6+card_4))).
     *
     * @param  array<string, mixed>  $payload  email, order, card, sign
     */
    public function verifyCallbackSignature(array $payload): bool
    {
        $email = (string) ($payload['email'] ?? '');
        $order = (string) ($payload['order'] ?? '');
        $card = (string) ($payload['card'] ?? '');

        $expected = md5(strtoupper(
            strrev($email).
            $this->password.
            $order.
            strrev(substr($card, 0, 6).substr($card, -4))
        ));

        return hash_equals($expected, strtolower($payload['sign'] ?? ''));
    }

    /**
     * data = base64(json{amount, description, currency}).
     */
    public function generateData(float $amount, string $description): string
    {
        return base64_encode((string) json_encode([
            'amount' => number_format($amount, 2, '.', ''),
            'description' => $description,
            'currency' => 'UAH',
        ]));
    }

    /**
     * Підпис форми: MD5(strtoupper(strrev(key) + strrev(payment) + strrev(data) + strrev(url) + strrev(password))).
     */
    public function signForm(string $data, string $callbackUrl): string
    {
        return md5(strtoupper(
            strrev($this->key).
            strrev(self::PAYMENT_CODE).
            strrev($data).
            strrev($callbackUrl).
            strrev($this->password)
        ));
    }

    private function signStatus(string $orderId): string
    {
        return md5(strtoupper(
            strrev($this->key).strrev($orderId).strrev($this->password)
        ));
    }
}
