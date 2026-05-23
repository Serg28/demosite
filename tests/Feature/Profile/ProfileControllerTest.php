<?php

namespace Tests\Feature\Profile;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_profile_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.index'))
            ->assertOk()
            ->assertSee('profile');
    }

    public function test_authenticated_user_can_view_orders_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.orders'))
            ->assertOk();
    }

    public function test_authenticated_user_can_view_security_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.security'))
            ->assertOk();
    }

    public function test_user_can_view_own_order_details(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('profile.orders.details', $order->id))
            ->assertOk();
    }

    public function test_user_cannot_view_other_users_order(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user)
            ->get(route('profile.orders.details', $order->id))
            ->assertNotFound();
    }

    public function test_logout_redirects_to_home(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
