<?php

namespace Tests\Feature\Checkout;

use App\Actions\Checkout\AutoDetectDiscountCard;
use App\Models\DiscountCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoDetectDiscountCardTest extends TestCase
{
    use RefreshDatabase;

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
}
