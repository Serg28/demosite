<?php

namespace Tests\Feature\Checkout;

use App\Actions\Checkout\ApplyDiscountCard;
use App\Actions\Checkout\ApplyPromoCode;
use App\Models\DiscountCard;
use App\Models\PromoCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountActionsTest extends TestCase
{
    use RefreshDatabase;

    // ─── ApplyDiscountCard ───────────────────────────────────────────────────

    public function test_apply_discount_card_by_code(): void
    {
        DiscountCard::create(['code' => 'CARD123', 'type' => 'percent', 'value' => 10, 'is_active' => true]);

        $result = (new ApplyDiscountCard)->handle('CARD123', 1000.0);

        $this->assertTrue($result['success']);
        $this->assertEquals(100.0, $result['discount']);
    }

    public function test_apply_discount_card_by_barcode(): void
    {
        DiscountCard::create(['code' => 'CARD001', 'barcode' => 'BAR999', 'type' => 'fixed', 'value' => 50, 'is_active' => true]);

        $result = (new ApplyDiscountCard)->handle('BAR999', 1000.0);

        $this->assertTrue($result['success']);
        $this->assertEquals(50.0, $result['discount']);
    }

    public function test_apply_discount_card_by_phone_with_country_code(): void
    {
        DiscountCard::create(['code' => 'CARD002', 'phone' => '+380671234567', 'type' => 'percent', 'value' => 5, 'is_active' => true]);

        $result = (new ApplyDiscountCard)->handle('+380671234567', 1000.0);

        $this->assertTrue($result['success']);
    }

    public function test_apply_discount_card_by_local_phone_normalizes_to_country_code(): void
    {
        DiscountCard::create(['code' => 'CARD003', 'phone' => '+380671234567', 'type' => 'percent', 'value' => 5, 'is_active' => true]);

        // 0671234567 → normalizes to 380671234567 → matches '+380671234567'
        $result = (new ApplyDiscountCard)->handle('0671234567', 1000.0);

        $this->assertTrue($result['success']);
    }

    public function test_apply_discount_card_not_found(): void
    {
        $result = (new ApplyDiscountCard)->handle('UNKNOWN', 1000.0);

        $this->assertFalse($result['success']);
    }

    public function test_apply_discount_card_inactive_not_found(): void
    {
        DiscountCard::create(['code' => 'INACTIVE', 'type' => 'percent', 'value' => 10, 'is_active' => false]);

        $result = (new ApplyDiscountCard)->handle('INACTIVE', 1000.0);

        $this->assertFalse($result['success']);
    }

    // ─── ApplyPromoCode ──────────────────────────────────────────────────────

    public function test_apply_promo_code_success(): void
    {
        PromoCode::create(['code' => 'PROMO10', 'type' => 'percent', 'value' => 10, 'is_active' => true]);

        $result = (new ApplyPromoCode)->handle('PROMO10', 1000.0);

        $this->assertTrue($result['success']);
        $this->assertEquals(100.0, $result['discount']);
    }

    public function test_apply_promo_code_once_already_used(): void
    {
        PromoCode::create(['code' => 'ONCE01', 'type' => 'percent', 'value' => 10, 'is_active' => true, 'usage_type' => 'once', 'is_used' => true]);

        $result = (new ApplyPromoCode)->handle('ONCE01', 1000.0);

        $this->assertFalse($result['success']);
    }

    public function test_apply_promo_code_once_not_yet_used(): void
    {
        PromoCode::create(['code' => 'ONCE02', 'type' => 'percent', 'value' => 10, 'is_active' => true, 'usage_type' => 'once', 'is_used' => false]);

        $result = (new ApplyPromoCode)->handle('ONCE02', 1000.0);

        $this->assertTrue($result['success']);
    }

    public function test_apply_promo_code_max_uses_reached(): void
    {
        PromoCode::create(['code' => 'LIMITED', 'type' => 'percent', 'value' => 10, 'is_active' => true, 'max_uses' => 5, 'used_count' => 5]);

        $result = (new ApplyPromoCode)->handle('LIMITED', 1000.0);

        $this->assertFalse($result['success']);
    }

    public function test_apply_promo_code_max_uses_not_reached(): void
    {
        PromoCode::create(['code' => 'PARTIAL', 'type' => 'percent', 'value' => 10, 'is_active' => true, 'max_uses' => 5, 'used_count' => 3]);

        $result = (new ApplyPromoCode)->handle('PARTIAL', 1000.0);

        $this->assertTrue($result['success']);
    }

    public function test_apply_promo_code_no_max_uses_limit(): void
    {
        PromoCode::create(['code' => 'UNLIMITED', 'type' => 'fixed', 'value' => 50, 'is_active' => true, 'max_uses' => null, 'used_count' => 9999]);

        $result = (new ApplyPromoCode)->handle('UNLIMITED', 1000.0);

        $this->assertTrue($result['success']);
    }

    public function test_apply_promo_code_expired(): void
    {
        PromoCode::create(['code' => 'EXPIRED', 'type' => 'percent', 'value' => 10, 'is_active' => true, 'expires_at' => now()->subDay()]);

        $result = (new ApplyPromoCode)->handle('EXPIRED', 1000.0);

        $this->assertFalse($result['success']);
    }

    // ─── isCompatibleWith ─────────────────────────────────────────────────────

    public function test_discount_card_not_compatible_with_promo_when_use_for_promotional_false(): void
    {
        $card = new DiscountCard(['code' => 'DC', 'type' => 'percent', 'value' => 5, 'use_for_promotional' => false]);
        $promo = new PromoCode(['code' => 'P', 'type' => 'percent', 'value' => 10]);

        $this->assertFalse($card->isCompatibleWith($promo));
    }

    public function test_promo_code_not_compatible_with_discount_card_when_use_for_discount_cards_false(): void
    {
        $promo = new PromoCode(['code' => 'P', 'type' => 'percent', 'value' => 10, 'use_for_discount_cards' => false]);
        $card = new DiscountCard(['code' => 'DC', 'type' => 'percent', 'value' => 5]);

        $this->assertFalse($promo->isCompatibleWith($card));
    }
}
