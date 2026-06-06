<?php

namespace Tests\Feature\Livewire\Favorite;

use App\Livewire\Favorite\Lists;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_favorites_list(): void
    {
        Livewire::test(Lists::class)
            ->assertRedirect();
    }

    public function test_authenticated_user_sees_wishlist(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();

        Wishlist::create(['user_id' => $user->id, 'product_id' => $product->id]);

        Livewire::actingAs($user)
            ->test(Lists::class)
            ->assertSee($product->t('title'));
    }

    public function test_empty_wishlist_shows_placeholder(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Lists::class)
            ->assertSee(__t('Список бажань порожній'));
    }

    public function test_wishlist_count_computed(): void
    {
        $user     = User::factory()->create();
        $products = Product::factory()->count(3)->create();

        foreach ($products as $p) {
            Wishlist::create(['user_id' => $user->id, 'product_id' => $p->id]);
        }

        $component = Livewire::actingAs($user)->test(Lists::class);
        $this->assertEquals(3, $component->get('totalCount'));
    }
}
