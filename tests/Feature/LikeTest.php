<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_gets_401_on_toggle(): void
    {
        $product = Product::factory()->create();

        $this->postJson(route('like.toggle', $product))
            ->assertStatus(401);
    }

    public function test_auth_user_can_add_to_wishlist(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)
            ->postJson(route('like.toggle', $product))
            ->assertStatus(200)
            ->assertJsonFragment(['isActive' => true, 'action' => 'add', 'status' => 'success']);

        $this->assertDatabaseHas('wishlists', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_auth_user_can_remove_from_wishlist(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();

        Wishlist::create(['user_id' => $user->id, 'product_id' => $product->id]);

        $this->actingAs($user)
            ->postJson(route('like.toggle', $product))
            ->assertStatus(200)
            ->assertJsonFragment(['isActive' => false, 'action' => 'remove']);

        $this->assertDatabaseMissing('wishlists', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_status_returns_favorites_for_auth_user(): void
    {
        $user     = User::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        Wishlist::create(['user_id' => $user->id, 'product_id' => $product1->id]);

        $this->actingAs($user)
            ->postJson(route('like.status'), ['productIds' => [$product1->id, $product2->id]])
            ->assertStatus(200)
            ->assertJsonFragment(['favorites' => [$product1->id]]);
    }
}
