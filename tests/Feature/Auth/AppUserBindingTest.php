<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppUserBindingTest extends TestCase
{
    use RefreshDatabase;

    public function test_app_user_returns_null_for_guest(): void
    {
        $this->assertNull(app('user'));
    }

    public function test_app_user_returns_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertInstanceOf(User::class, app('user'));
        $this->assertEquals($user->id, app('user')->id);
    }

    public function test_has_password_returns_true_for_user_with_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret')]);
        $this->assertTrue($user->hasPassword());
    }

    public function test_has_password_returns_false_for_otp_user(): void
    {
        $user = User::factory()->create(['password' => null]);
        $this->assertFalse($user->hasPassword());
    }

    public function test_auth_routes_require_authentication(): void
    {
        $this->get('/profile')->assertRedirect(route('login'));
        $this->get('/profile/orders')->assertRedirect(route('login'));
    }
}
