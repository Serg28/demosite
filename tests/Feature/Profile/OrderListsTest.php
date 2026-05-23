<?php

namespace Tests\Feature\Profile;

use App\Livewire\Profile\Order\Lists;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderListsTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders_empty_state(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Lists::class)
            ->assertOk();
    }

    public function test_shows_user_orders(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Lists::class)
            ->assertSee('#');
    }

    public function test_does_not_show_other_users_orders(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Order::factory()->create([
            'user_id'    => $otherUser->id,
            'first_name' => 'ЧужийКористувач',
        ]);
        Order::factory()->create([
            'user_id'    => $user->id,
            'first_name' => 'МійКористувач',
        ]);

        $component = Livewire::actingAs($user)->test(Lists::class);

        $this->assertCount(1, $component->get('dats'));
    }

    public function test_search_filters_by_id(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        Order::factory()->count(2)->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Lists::class)
            ->set('search', (string) $order->id)
            ->assertSee('#' . $order->id);
    }

    public function test_show_more_loads_additional_orders(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Lists::class, ['limit' => 2])
            ->assertSet('more', false)
            ->call('showMore')
            ->assertSet('more', true);
    }
}
