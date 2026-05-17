<?php

namespace Tests\Feature\Checkout;

use App\Actions\Checkout\ApplyPromoCode;
use App\Models\Product;
use App\Models\PromoCode;
use App\Services\Cart\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PromoCodeProductRestrictionTest extends TestCase
{
    use RefreshDatabase;

    public function test_promo_applies_when_no_product_restrictions(): void
    {
        $promo = PromoCode::factory()->create([
            'code'      => 'FREE10',
            'is_active' => true,
            'type'      => 'percent',
            'value'     => 10,
        ]);

        $result = app(ApplyPromoCode::class)->handle('FREE10', 500.0);

        $this->assertTrue($result['success']);
        $this->assertEquals(50.0, $result['discount']);
    }

    public function test_promo_rejected_when_cart_has_no_matching_restricted_products(): void
    {
        $product = Product::factory()->create();
        $promo   = PromoCode::factory()->create([
            'code'      => 'RESTRICTED',
            'is_active' => true,
            'type'      => 'percent',
            'value'     => 15,
        ]);
        $promo->products()->attach($product->id);

        $cartService = Mockery::mock(CartService::class);
        $cartService->shouldReceive('getIdsProductsInCart')->andReturn([999]);
        $this->app->instance(CartService::class, $cartService);

        $result = app(ApplyPromoCode::class)->handle('RESTRICTED', 500.0);

        $this->assertFalse($result['success']);
    }

    public function test_promo_applies_when_cart_has_matching_restricted_product(): void
    {
        $product = Product::factory()->create();
        $promo   = PromoCode::factory()->create([
            'code'      => 'MATCH',
            'is_active' => true,
            'type'      => 'percent',
            'value'     => 20,
        ]);
        $promo->products()->attach($product->id);

        $cartService = Mockery::mock(CartService::class);
        $cartService->shouldReceive('getIdsProductsInCart')->andReturn([$product->id]);
        $this->app->instance(CartService::class, $cartService);

        $result = app(ApplyPromoCode::class)->handle('MATCH', 500.0);

        $this->assertTrue($result['success']);
        $this->assertEquals([$product->id], $result['restrictedProductIds']);
    }
}
