<?php

namespace Tests\Feature\Profile;

use App\Livewire\Profile\Discount\Lists;
use App\Models\CumulativeDiscount;
use App\Models\Order;
use App\Models\User;
use App\Observers\OrderObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CumulativeDiscountTest extends TestCase
{
    use RefreshDatabase;

    // ── User::effectiveDiscount ───────────────────────────────────────────────

    public function test_effective_discount_returns_zero_when_null(): void
    {
        $user = User::factory()->create(['discount_cumulative' => null]);

        $this->assertSame(0, $user->effectiveDiscount());
    }

    public function test_effective_discount_returns_stored_value(): void
    {
        $user = User::factory()->create(['discount_cumulative' => 7]);

        $this->assertSame(7, $user->effectiveDiscount());
    }

    // ── OrderObserver ─────────────────────────────────────────────────────────

    public function test_observer_recalculates_discount_on_completion(): void
    {
        CumulativeDiscount::create(['total_sum' => 1000, 'discount' => 3]);
        CumulativeDiscount::create(['total_sum' => 5000, 'discount' => 7]);

        $user  = User::factory()->create(['discount_cumulative' => 0]);
        $order = Order::factory()->create([
            'user_id'         => $user->id,
            'order_status_id' => 1,
            'cost'            => 2000,
        ]);

        // Переводимо в статус «Виконано» (11)
        $order->update(['order_status_id' => 11]);

        $this->assertSame(3, $user->fresh()->discount_cumulative);
    }

    public function test_observer_picks_highest_applicable_level(): void
    {
        CumulativeDiscount::create(['total_sum' => 1000, 'discount' => 3]);
        CumulativeDiscount::create(['total_sum' => 5000, 'discount' => 7]);
        CumulativeDiscount::create(['total_sum' => 10000, 'discount' => 10]);

        $user = User::factory()->create(['discount_cumulative' => 0]);

        // Два виконаних замовлення на суму 6000
        Order::factory()->create(['user_id' => $user->id, 'order_status_id' => 11, 'cost' => 3000]);
        $order = Order::factory()->create(['user_id' => $user->id, 'order_status_id' => 1, 'cost' => 3000]);

        $order->update(['order_status_id' => 11]);

        $this->assertSame(7, $user->fresh()->discount_cumulative);
    }

    public function test_observer_does_not_recalculate_on_non_completed_status(): void
    {
        CumulativeDiscount::create(['total_sum' => 1000, 'discount' => 5]);

        $user  = User::factory()->create(['discount_cumulative' => 0]);
        $order = Order::factory()->create([
            'user_id'         => $user->id,
            'order_status_id' => 1,
            'cost'            => 2000,
        ]);

        // Переводимо в статус «Скасовано» (12), а не «Виконано»
        $order->update(['order_status_id' => 12]);

        $this->assertSame(0, $user->fresh()->discount_cumulative);
    }

    public function test_observer_no_matching_level_sets_zero(): void
    {
        CumulativeDiscount::create(['total_sum' => 10000, 'discount' => 5]);

        $user  = User::factory()->create(['discount_cumulative' => 3]);
        $order = Order::factory()->create([
            'user_id'         => $user->id,
            'order_status_id' => 1,
            'cost'            => 500,
        ]);

        $order->update(['order_status_id' => 11]);

        // Сума 500 не досягає порогу 10000 → знижка 0
        $this->assertSame(0, $user->fresh()->discount_cumulative);
    }

    // ── Profile Discount Lists ────────────────────────────────────────────────

    public function test_discount_lists_shows_cumulative_levels(): void
    {
        CumulativeDiscount::create(['total_sum' => 1000, 'discount' => 3]);

        $user = User::factory()->create(['discount_cumulative' => 3]);

        Livewire::actingAs($user)
            ->test(Lists::class)
            ->assertSee('3%');
    }

    public function test_best_discount_uses_max_of_card_and_cumulative(): void
    {
        CumulativeDiscount::create(['total_sum' => 100, 'discount' => 10]);

        $user = User::factory()->create(['discount_cumulative' => 10]);

        $component = Livewire::actingAs($user)->test(Lists::class);

        $this->assertSame(10, $component->get('bestDiscount'));
    }
}
