<?php

namespace Tests\Feature\Checkout;

use App\Actions\Checkout\AutoDetectDiscountCard;
use App\Models\DiscountCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AutoDetectDiscountCardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_finds_card_by_normalized_phone(): void
    {
        DiscountCard::factory()->create(['phone' => '+380671234567', 'is_active' => true]);

        $result = app(AutoDetectDiscountCard::class)->handle('0671234567');

        $this->assertNotNull($result);
    }

    public function test_finds_card_by_full_phone_with_plus(): void
    {
        DiscountCard::factory()->create(['phone' => '+380671234567', 'is_active' => true]);

        $result = app(AutoDetectDiscountCard::class)->handle('+380671234567');

        $this->assertNotNull($result);
    }

    public function test_returns_null_when_not_found(): void
    {
        $result = app(AutoDetectDiscountCard::class)->handle('0991112233');

        $this->assertNull($result);
    }

    public function test_returns_null_for_inactive_card(): void
    {
        DiscountCard::factory()->create(['phone' => '+380991112233', 'is_active' => false]);

        $result = app(AutoDetectDiscountCard::class)->handle('0991112233');

        $this->assertNull($result);
    }

    public function test_returns_null_for_too_short_phone(): void
    {
        $result = app(AutoDetectDiscountCard::class)->handle('12345');

        $this->assertNull($result);
    }

    public function test_phone_lookup_is_cached_on_repeat_call(): void
    {
        DiscountCard::factory()->create(['phone' => '+380671234567', 'is_active' => true]);

        DB::enableQueryLog();
        app(AutoDetectDiscountCard::class)->handle('0671234567');
        $firstQueryCount = count(DB::getQueryLog());

        DB::flushQueryLog();
        app(AutoDetectDiscountCard::class)->handle('0671234567');
        $secondQueryCount = count(DB::getQueryLog());

        DB::disableQueryLog();

        // First call: phone OR-lookup + find by PK; second call: only find by PK (lookup cached)
        $this->assertGreaterThan(1, $firstQueryCount, 'First call should hit the DB for phone lookup');
        $this->assertSame(1, $secondQueryCount, 'Second call should skip phone lookup (cached), only fetch by PK');
    }
}
