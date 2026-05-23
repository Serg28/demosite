<?php

namespace Tests\Feature\Profile;

use App\Livewire\Profile\User\Security;
use App\Models\User;
use App\Models\UserProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Security::class)
            ->assertOk();
    }

    public function test_detects_two_factor_enabled(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Security::class)
            ->assertSet('twoFactorEnabled', true);
    }

    public function test_detects_two_factor_disabled(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();

        Livewire::actingAs($user)
            ->test(Security::class)
            ->assertSet('twoFactorEnabled', false);
    }

    public function test_confirm_disable_shows_confirmation(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Security::class)
            ->assertSet('showDisableConfirm', false)
            ->call('confirmDisable')
            ->assertSet('showDisableConfirm', true)
            ->call('cancelDisable')
            ->assertSet('showDisableConfirm', false);
    }

    public function test_can_disconnect_provider_when_has_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $provider = UserProvider::create([
            'user_id'     => $user->id,
            'provider'    => 'google',
            'provider_id' => 'google-123',
            'name'        => 'Test User',
        ]);

        Livewire::actingAs($user)
            ->test(Security::class)
            ->call('disconnectProvider', $provider->id);

        $this->assertDatabaseMissing('user_providers', ['id' => $provider->id]);
    }

    public function test_cannot_disconnect_only_provider_without_password(): void
    {
        $user = User::factory()->create(['password' => null]);
        $provider = UserProvider::create([
            'user_id'     => $user->id,
            'provider'    => 'google',
            'provider_id' => 'google-only',
            'name'        => 'Test User',
        ]);

        Livewire::actingAs($user)
            ->test(Security::class)
            ->call('disconnectProvider', $provider->id);

        $this->assertDatabaseHas('user_providers', ['id' => $provider->id]);
    }

    public function test_cannot_disconnect_another_users_provider(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $otherUser = User::factory()->create();
        $provider = UserProvider::create([
            'user_id'     => $otherUser->id,
            'provider'    => 'google',
            'provider_id' => 'google-other',
        ]);

        Livewire::actingAs($user)
            ->test(Security::class)
            ->call('disconnectProvider', $provider->id);

        $this->assertDatabaseHas('user_providers', ['id' => $provider->id]);
    }
}
