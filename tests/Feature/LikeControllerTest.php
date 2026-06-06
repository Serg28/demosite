<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── toggle ────────────────────────────────────────────────

    public function test_toggle_adds_product_to_wishlist(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('like.toggle', $product));

        $response->assertOk()
            ->assertJsonFragment(['isActive' => true, 'action' => 'add', 'status' => 'success']);

        $this->assertDatabaseHas('wishlists', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_toggle_removes_product_from_wishlist(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();
        Wishlist::create(['user_id' => $user->id, 'product_id' => $product->id]);

        $response = $this->actingAs($user)
            ->postJson(route('like.toggle', $product));

        $response->assertOk()
            ->assertJsonFragment(['isActive' => false, 'action' => 'remove']);

        $this->assertDatabaseMissing('wishlists', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_toggle_requires_auth(): void
    {
        $product = Product::factory()->create();

        $this->postJson(route('like.toggle', $product))
            ->assertUnauthorized();
    }

    // ─── status ────────────────────────────────────────────────

    public function test_status_returns_favorite_product_ids(): void
    {
        $user     = User::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $product3 = Product::factory()->create();

        Wishlist::create(['user_id' => $user->id, 'product_id' => $product1->id]);
        Wishlist::create(['user_id' => $user->id, 'product_id' => $product3->id]);

        $response = $this->actingAs($user)
            ->postJson(route('like.status'), [
                'productIds' => [$product1->id, $product2->id, $product3->id],
            ]);

        $response->assertOk();
        $favorites = $response->json('favorites');

        $this->assertContains($product1->id, $favorites);
        $this->assertContains($product3->id, $favorites);
        $this->assertNotContains($product2->id, $favorites);
    }

    public function test_status_returns_empty_array_for_no_products(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('like.status'), ['productIds' => []])
            ->assertOk()
            ->assertJsonFragment(['favorites' => []]);
    }

    public function test_status_returns_empty_favorites_for_guest(): void
    {
        // Гості отримують 200 з порожнім масивом — JS обробляє це коректно (updateStatuses).
        $this->postJson(route('like.status'), ['productIds' => [1, 2, 3]])
            ->assertOk()
            ->assertJsonFragment(['favorites' => []]);
    }
}
