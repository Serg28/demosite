<?php

namespace Tests\Feature\Favorite;

use App\Livewire\Favorite\Lists;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FavoriteListsTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.favorites'))
            ->assertOk()
            ->assertSeeLivewire(Lists::class);
    }

    public function test_redirects_guest_to_login(): void
    {
        $this->get(route('profile.favorites'))
            ->assertRedirect(route('login'));
    }

    public function test_shows_favorite_products(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['title' => 'Test Product']);
        Wishlist::create(['user_id' => $user->id, 'product_id' => $product->id]);

        Livewire::actingAs($user)
            ->test(Lists::class)
            ->assertSee('Test Product');
    }

    public function test_shows_empty_state_when_no_favorites(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Lists::class)
            ->assertSee(__t('Список бажань порожній'));
    }

    public function test_total_count_reflects_wishlist_count(): void
    {
        $user     = User::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        Wishlist::create(['user_id' => $user->id, 'product_id' => $product1->id]);
        Wishlist::create(['user_id' => $user->id, 'product_id' => $product2->id]);

        $component = Livewire::actingAs($user)->test(Lists::class);

        $this->assertSame(2, $component->get('totalCount'));
    }

    public function test_refresh_on_update_favorite_count_event(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();

        Livewire::actingAs($user)
            ->test(Lists::class)
            ->dispatch('updateFavoriteCount')
            ->assertOk();
    }
}
