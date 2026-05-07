<?php

namespace Tests\Unit\Unit\Services;

use App\Services\CurrencyService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('currency_settings');
    }

    public function test_returns_default_code_and_symbol(): void
    {
        Cache::put('currency_settings', ['code' => 'UAH', 'symbol' => '₴'], 3600);

        $service = new CurrencyService;

        $this->assertSame('UAH', $service->code());
        $this->assertSame('₴', $service->symbol());
    }

    public function test_format_whole_number_without_decimals(): void
    {
        Cache::put('currency_settings', ['code' => 'UAH', 'symbol' => '₴'], 3600);

        $service = new CurrencyService;

        $this->assertSame("1\u{00A0}000", $service->format(1000.0));
    }

    public function test_format_fractional_with_two_decimals(): void
    {
        Cache::put('currency_settings', ['code' => 'UAH', 'symbol' => '₴'], 3600);

        $service = new CurrencyService;

        $result = $service->format(1000.50);

        $this->assertStringContainsString(',50', $result);
    }

    public function test_format_with_symbol_appends_symbol(): void
    {
        Cache::put('currency_settings', ['code' => 'UAH', 'symbol' => '₴'], 3600);

        $service = new CurrencyService;

        $this->assertStringEndsWith('₴', $service->formatWithSymbol(100.0));
    }

    public function test_flush_cache_removes_key(): void
    {
        Cache::put('currency_settings', ['code' => 'UAH', 'symbol' => '₴'], 3600);

        $service = new CurrencyService;
        $service->flushCache();

        $this->assertFalse(Cache::has('currency_settings'));
    }
}
