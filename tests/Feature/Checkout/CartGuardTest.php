<?php

namespace Tests\Feature\Checkout;

use App\Models\Product;
use App\Services\Checkout\CartGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CartGuardTest extends TestCase
{
    use RefreshDatabase;

    private CartGuard $guard;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guard = app(CartGuard::class);
    }

    private function makeItem(int $id, string $name, float $price, string $rowId = 'row1'): object
    {
        return (object) ['rowId' => $rowId, 'id' => $id, 'name' => $name, 'price' => $price];
    }

    public function test_returns_empty_for_empty_cart(): void
    {
        $result = $this->guard->check(collect());

        $this->assertSame([], $result['out_of_stock']);
        $this->assertSame([], $result['price_changed']);
    }

    public function test_detects_out_of_stock_product(): void
    {
        $product = Product::factory()->create(['price' => 100.00, 'is_active' => true, 'quantity' => 0]);

        $result = $this->guard->check(collect([$this->makeItem($product->id, 'Test Product', 100.00)]));

        $this->assertCount(1, $result['out_of_stock']);
        $this->assertSame($product->id, $result['out_of_stock'][0]['id']);
        $this->assertSame([], $result['price_changed']);
    }

    public function test_detects_inactive_product(): void
    {
        $product = Product::factory()->create(['price' => 100.00, 'is_active' => false, 'quantity' => 5]);

        $result = $this->guard->check(collect([$this->makeItem($product->id, 'Test Product', 100.00)]));

        $this->assertCount(1, $result['out_of_stock']);
    }

    public function test_detects_missing_product(): void
    {
        $result = $this->guard->check(collect([$this->makeItem(99999, 'Ghost product', 100.00)]));

        $this->assertCount(1, $result['out_of_stock']);
    }

    public function test_detects_price_change(): void
    {
        $product = Product::factory()->create(['price' => 150.00, 'is_active' => true, 'quantity' => 5]);

        $result = $this->guard->check(collect([$this->makeItem($product->id, 'Test Product', 100.00)]));

        $this->assertCount(1, $result['price_changed']);
        $this->assertSame($product->id, $result['price_changed'][0]['id']);
        $this->assertEqualsWithDelta(150.00, $result['price_changed'][0]['newPrice'], 0.01);
        $this->assertEqualsWithDelta(100.00, $result['price_changed'][0]['oldPrice'], 0.01);
        $this->assertSame([], $result['out_of_stock']);
    }

    public function test_skips_product_with_acceptable_price(): void
    {
        $product = Product::factory()->create(['price' => 100.00, 'is_active' => true, 'quantity' => 3]);

        $result = $this->guard->check(collect([$this->makeItem($product->id, 'Test Product', 100.00)]));

        $this->assertSame([], $result['out_of_stock']);
        $this->assertSame([], $result['price_changed']);
    }

    public function test_ignores_price_diff_within_tolerance(): void
    {
        $product = Product::factory()->create(['price' => 100.00, 'is_active' => true, 'quantity' => 3]);

        $result = $this->guard->check(collect([$this->makeItem($product->id, 'Test Product', 100.005)]));

        $this->assertSame([], $result['price_changed']);
    }

    public function test_mixed_items_correctly_categorised(): void
    {
        $ok      = Product::factory()->create(['price' => 100.00, 'is_active' => true, 'quantity' => 3]);
        $oos     = Product::factory()->create(['price' => 200.00, 'is_active' => true, 'quantity' => 0]);
        $changed = Product::factory()->create(['price' => 300.00, 'is_active' => true, 'quantity' => 2]);

        $items = collect([
            $this->makeItem($ok->id, 'OK Product', 100.00, 'r1'),
            $this->makeItem($oos->id, 'OOS Product', 200.00, 'r2'),
            $this->makeItem($changed->id, 'Changed Product', 250.00, 'r3'),
        ]);

        $result = $this->guard->check($items);

        $this->assertCount(1, $result['out_of_stock']);
        $this->assertCount(1, $result['price_changed']);
        $this->assertSame($oos->id, $result['out_of_stock'][0]['id']);
        $this->assertSame($changed->id, $result['price_changed'][0]['id']);
    }
}
