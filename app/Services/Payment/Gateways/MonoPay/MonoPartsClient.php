<?php

namespace App\Services\Payment\Gateways\MonoPay;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonoPartsClient
{
    public function __construct(
        private readonly string $storeId,
        private readonly string $secret,
        private readonly string $apiUrl = 'https://u2-demo-ext.mono.st4g3.com',
    ) {}

    public function create(array $body): ?array
    {
        return $this->post('/api/order/create', $body);
    }

    public function state(string $orderId): ?array
    {
        return $this->post('/api/order/state', ['order_id' => $orderId]);
    }

    public function confirm(string $orderId): ?array
    {
        return $this->post('/api/order/confirm', ['order_id' => $orderId]);
    }

    public function reject(string $orderId): ?array
    {
        return $this->post('/api/order/reject', ['order_id' => $orderId]);
    }

    public function returnOrder(string $orderId, string $storeReturnId, float $sum): ?array
    {
        return $this->post('/api/order/return', [
            'order_id'              => $orderId,
            'return_money_to_card'  => true,
            'store_return_id'       => $storeReturnId,
            'sum'                   => round($sum, 2),
        ]);
    }

    public function verifyWebhookSignature(string $rawBody, string $signature): bool
    {
        $expected = $this->sign($rawBody);

        return hash_equals($expected, $signature);
    }

    private function post(string $path, array $body): ?array
    {
        $json = json_encode($body, JSON_UNESCAPED_UNICODE);

        try {
            $response = Http::asJson()
                ->withHeaders([
                    'store-id'  => $this->storeId,
                    'signature' => $this->sign($json),
                ])
                ->post(rtrim($this->apiUrl, '/') . $path, $body);

            return $response->json();
        } catch (\Throwable $e) {
            Log::channel('payments')->error('MonoParts API error', [
                'path'  => $path,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function sign(string $body): string
    {
        return base64_encode(hash_hmac('sha256', $body, $this->secret, true));
    }
}
