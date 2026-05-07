<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

final class CurrencyService
{
    private const CACHE_KEY = 'currency_settings';

    private const CACHE_TTL = 3600;

    private string $code;

    private string $symbol;

    public function __construct()
    {
        $config = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function (): array {
            return [
                'code'   => (string) (setting('currency_code') ?: 'UAH'),
                'symbol' => (string) (setting('currency') ?: '₴'),
            ];
        });

        $this->code   = $config['code'];
        $this->symbol = $config['symbol'];
    }

    public function code(): string
    {
        return $this->code;
    }

    public function symbol(): string
    {
        return $this->symbol;
    }

    /** Форматує число (авто: 0 або 2 знаки після коми). */
    public function format(float $amount, int $decimals = -1): string
    {
        if ($decimals < 0) {
            $cents    = (int) round($amount * 100) % 100;
            $decimals = $cents !== 0 ? 2 : 0;
        }

        return number_format($amount, $decimals, ',', "\u{00A0}");
    }

    /** Форматує число з символом валюти. */
    public function formatWithSymbol(float $amount, int $decimals = -1): string
    {
        return $this->format($amount, $decimals).' '.$this->symbol;
    }

    /** Скидає кеш налаштувань (викликати після зміни Setting). */
    public function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
