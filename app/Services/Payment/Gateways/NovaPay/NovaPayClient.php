<?php

namespace App\Services\Payment\Gateways\NovaPay;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NovaPayClient
{
    public function __construct(
        private readonly string $merchantId,
        private readonly string $privateKey,
        private readonly string $apiUrl,
    ) {}

    /**
     * Крок 1 — створює checkout-сесію.
     * Повертає масив з полями id, url тощо.
     *
     * @param  array<string, mixed>  $params  merchant_id, callback_url, success_url, fail_url, delivery
     * @return array<string, mixed>|null
     */
    public function createCheckoutSession(array $params): ?array
    {
        $body = json_encode($params, JSON_UNESCAPED_UNICODE);
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Sign' => $this->sign((string) $body),
        ])->timeout(10)->withBody((string) $body, 'application/json')
            ->post($this->apiUrl.'/checkout/session');

        if (! $response->successful()) {
            Log::channel('payments')->warning('NovaPay: createCheckoutSession error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json();
    }

    /**
     * Крок 2 — додає платіж до сесії.
     * Повертає масив з полем url для редіректу.
     *
     * @param  array<string, mixed>  $params  merchant_id, session_id, amount, external_id, use_hold, products
     * @return array<string, mixed>|null
     */
    public function addCheckoutPayment(array $params): ?array
    {
        $body = json_encode($params, JSON_UNESCAPED_UNICODE);
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Sign' => $this->sign((string) $body),
        ])->timeout(10)->withBody((string) $body, 'application/json')
            ->post($this->apiUrl.'/checkout/payment');

        if (! $response->successful()) {
            Log::channel('payments')->warning('NovaPay: addCheckoutPayment error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json();
    }

    /**
     * Підписує JSON-рядок RSA-SHA1 приватним ключем мерчанта.
     * Точно відповідає алгоритму NovaPay (openssl_sign + OPENSSL_ALGO_SHA1).
     */
    public function sign(string $json): string
    {
        $privateKey = openssl_pkey_get_private($this->privateKey);

        if (! $privateKey) {
            return '';
        }

        $signature = '';
        openssl_sign($json, $signature, $privateKey, OPENSSL_ALGO_SHA1);

        return base64_encode($signature);
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }
}
