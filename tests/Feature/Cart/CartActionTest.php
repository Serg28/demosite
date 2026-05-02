<?php

namespace Tests\Feature\Cart;

use App\Models\Product;
use Linecore\Shoppingcart\Facades\Cart;
use Tests\TestCase;

class CartActionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cart::destroy();
    }

    public function test_add_to_cart(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'quantity' => 10, 'price' => 100.00]);

        $response = $this->postJson("/cart/add/{$product->id}", ['count' => 2]);

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('action', 'add')
            ->assertJsonPath('count', 2);

        $this->assertCount(1, Cart::content());
        $this->assertEquals(2, Cart::count());
    }

    public function test_add_inactive_product_fails(): void
    {
        $product = Product::factory()->create(['is_active' => false, 'quantity' => 10]);

        $response = $this->postJson("/cart/add/{$product->id}", ['count' => 1]);

        $response->assertStatus(422)->assertJsonPath('status', false);
        $this->assertEquals(0, Cart::count());
    }

    public function test_update_cart_qty_increase(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'quantity' => 10, 'price' => 100.00]);
        $this->postJson("/cart/add/{$product->id}", ['count' => 1]);

        $rowId = Cart::content()->first()->rowId;
        $response = $this->patchJson("/cart/update/{$rowId}", ['qty' => 3]);

        // GA4 delta: qty зросла → action = 'add'
        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('action', 'add')
            ->assertJsonPath('count', 3);

        $this->assertEquals(3, Cart::count());
    }

    public function test_update_cart_qty_decrease(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'quantity' => 10, 'price' => 100.00]);
        $this->postJson("/cart/add/{$product->id}", ['count' => 5]);

        $rowId = Cart::content()->first()->rowId;
        $response = $this->patchJson("/cart/update/{$rowId}", ['qty' => 2]);

        // GA4 delta: qty зменшилась → action = 'remove'
        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('action', 'remove')
            ->assertJsonPath('count', 2);

        $this->assertEquals(2, Cart::count());
    }

    public function test_update_delta_qty_in_products(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'quantity' => 10, 'price' => 100.00]);
        $this->postJson("/cart/add/{$product->id}", ['count' => 2]);

        $rowId = Cart::content()->first()->rowId;
        $response = $this->patchJson("/cart/update/{$rowId}", ['qty' => 5]);

        $products = $response->json('products');
        // delta = |5 - 2| = 3
        $this->assertEquals(3, $products[0]['quantity']);
    }

    public function test_remove_from_cart(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'quantity' => 10, 'price' => 100.00]);
        $this->postJson("/cart/add/{$product->id}", ['count' => 2]);

        $rowId = Cart::content()->first()->rowId;
        $response = $this->deleteJson("/cart/remove/{$rowId}");

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('action', 'remove')
            ->assertJsonPath('count', 0);

        $this->assertEquals(0, Cart::count());
    }

    public function test_cart_result_has_ga4_products_array(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'quantity' => 10, 'price' => 99.50]);

        $response = $this->postJson("/cart/add/{$product->id}", ['count' => 2]);

        $response->assertOk();
        $products = $response->json('products');

        $this->assertIsArray($products);
        $this->assertCount(1, $products);
        $this->assertArrayHasKey('item_id', $products[0]);
        $this->assertArrayHasKey('item_name', $products[0]);
        $this->assertArrayHasKey('price', $products[0]);
        $this->assertArrayHasKey('quantity', $products[0]);
        $this->assertEquals(2, $products[0]['quantity']);
    }

    public function test_update_uses_qty_field(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'quantity' => 10, 'price' => 100.00]);
        $this->postJson("/cart/add/{$product->id}", ['count' => 1]);

        $rowId = Cart::content()->first()->rowId;
        $response = $this->patchJson("/cart/update/{$rowId}", ['qty' => 5]);

        $response->assertJsonPath('count', 5);
        $this->assertEquals(5, Cart::count());
    }
}
