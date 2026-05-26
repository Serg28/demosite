<?php

namespace Tests\Feature\Profile;

use App\Livewire\Profile\Discount\Lists;
use App\Models\DiscountCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DiscountListsTest extends TestCase
{
    use RefreshDatabase;

    public function test_discounts_page_requires_auth(): void
    {
        $this->get(route('profile.discounts'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_discounts_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.discounts'))
            ->assertOk()
            ->assertSeeLivewire(Lists::class);
    }

    public function test_component_renders_empty_state(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Lists::class)
            ->assertSee('Дисконтних карток поки немає');
    }

    public function test_component_renders_user_discount_cards(): void
    {
        $user = User::factory()->create();
        DiscountCard::factory()->create([
            'user_id'   => $user->id,
            'code'      => 'VIP2024',
            'type'      => 'percent',
            'value'     => 10,
            'is_active' => true,
        ]);

        Livewire::actingAs($user)
            ->test(Lists::class)
            ->assertSee('VIP2024')
            ->assertSee('10%');
    }

    public function test_inactive_cards_not_shown(): void
    {
        $user = User::factory()->create();
        DiscountCard::factory()->create([
            'user_id'   => $user->id,
            'code'      => 'INACTIVE',
            'type'      => 'percent',
            'value'     => 5,
            'is_active' => false,
        ]);

        Livewire::actingAs($user)
            ->test(Lists::class)
            ->assertDontSee('INACTIVE');
    }

    public function test_best_discount_computed(): void
    {
        $user = User::factory()->create();
        DiscountCard::factory()->create([
            'user_id'   => $user->id,
            'type'      => 'percent',
            'value'     => 7,
            'is_active' => true,
        ]);
        DiscountCard::factory()->create([
            'user_id'   => $user->id,
            'type'      => 'percent',
            'value'     => 12,
            'is_active' => true,
        ]);

        $component = Livewire::actingAs($user)->test(Lists::class);

        $this->assertEquals(12, $component->instance()->bestDiscount);
    }
}
