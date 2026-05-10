<?php

namespace App\Services\Payment\Gateways\MonoPay;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonoPayClient
{
    private const BASE_URL = 'https://api.monobank.ua';

    private const PUBKEY_CACHE_TTL = 86400; // 24h

    public function __construct(private readonly string $token) {}

    /**
     * Створює інвойс оплати.
     *
     * @param  array<string, mixed>  $params
     * @return array{invoiceId: string, pageUrl: string}|null
     */
    public function create(array $params): ?array
    {
        $response = $this->post('/api/merchant/invoice/create', $params);

        if ($response === null) {
            return null;
        }

        return [
            'invoiceId' => $response->invoiceId ?? '',
            'pageUrl'   => $response->pageUrl ?? '',
        ];
    }

    /**
     * Перевіряє статус інвойсу.
     */
    public function status(string $invoiceId): ?object
    {
        $response = Http::withHeader('X-Token', $this->token)
            ->timeout(10)
            ->get(self::BASE_URL . '/api/merchant/invoice/status', ['invoiceId' => $invoiceId]);

        if (! $response->successful()) {
            Log::channel('payments')->warning('MonoPay: status check failed', [
                'invoiceId'  => $invoiceId,
                'httpStatus' => $response->status(),
            ]);

            return null;
        }

        return $response->object();
    }

    /**
     * Підтверджує захоплення коштів (hold → capture).
     */
    public function finalize(string $invoiceId, int $amountKopecks): ?object
    {
        return $this->post('/api/merchant/invoice/finalize', [
            'invoiceId' => $invoiceId,
            'amount'    => $amountKopecks,
        ]);
    }

    /**
     * Скасовує інвойс або повертає кошти (refund).
     * Працює для статусів hold → release та success → refund.
     */
    public function cancel(string $invoiceId): ?object
    {
        return $this->post('/api/merchant/invoice/cancel', [
            'invoiceId' => $invoiceId,
        ]);
    }

    /**
     * Верифікує підпис webhook-а від MonoPay.
     * MonoPay підписує тіло запиту своїм RSA private key (SHA256).
     * Публічний ключ MonoPay отримується через /api/merchant/pubkey і кешується на 24h.
     */
    public function verifyWebhookSignature(string $body, string $xSign): bool
    {
        $pubkeyPem = $this->fetchPubkey();

        if ($pubkeyPem === null) {
            return false;
        }

        $publicKey = openssl_pkey_get_public($pubkeyPem);

        if ($publicKey === false) {
            return false;
        }

        $signatureBytes = base64_decode($xSign, true);

        return $signatureBytes !== false
            && openssl_verify($body, $signatureBytes, $publicKey, OPENSSL_ALGO_SHA256) === 1;
    }

    private function fetchPubkey(): ?string
    {
        return Cache::remember('monopay_webhook_pubkey', self::PUBKEY_CACHE_TTL, function (): ?string {
            $response = Http::withHeader('X-Token', $this->token)
                ->timeout(10)
                ->get(self::BASE_URL . '/api/merchant/pubkey');

            if (! $response->successful()) {
                Log::channel('payments')->error('MonoPay: failed to fetch webhook public key');

                return null;
            }

            return $response->body();
        });
    }

    /** @param array<string, mixed> $data */
    private function post(string $path, array $data): ?object
    {
        $response = Http::withHeader('X-Token', $this->token)
            ->timeout(10)
            ->post(self::BASE_URL . $path, $data);

        if (! $response->successful()) {
            Log::channel('payments')->warning('MonoPay API error', [
                'path'       => $path,
                'httpStatus' => $response->status(),
                'body'       => $response->body(),
            ]);

            return null;
        }

        return $response->object();
    }
}
