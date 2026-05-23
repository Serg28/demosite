<?php

namespace Tests\Feature\Profile;

use App\Livewire\Profile\User\EditPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class EditPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(EditPassword::class)
            ->assertOk();
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Livewire::actingAs($user)
            ->test(EditPassword::class)
            ->set('form.old_password', 'old-password')
            ->set('form.new_password', 'new-password-123')
            ->set('form.re_password', 'new-password-123')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_fails_with_wrong_old_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('real-password'),
        ]);

        Livewire::actingAs($user)
            ->test(EditPassword::class)
            ->set('form.old_password', 'wrong-password')
            ->set('form.new_password', 'new-password-123')
            ->set('form.re_password', 'new-password-123')
            ->call('submit')
            ->assertHasErrors(['form.old_password']);
    }

    public function test_fails_when_passwords_do_not_match(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Livewire::actingAs($user)
            ->test(EditPassword::class)
            ->set('form.old_password', 'old-password')
            ->set('form.new_password', 'new-password-123')
            ->set('form.re_password', 'different-password')
            ->call('submit')
            ->assertHasErrors(['form.re_password']);
    }

    public function test_validation_requires_all_fields(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(EditPassword::class)
            ->set('form.old_password', '')
            ->set('form.new_password', '')
            ->set('form.re_password', '')
            ->call('submit')
            ->assertHasErrors(['form.old_password', 'form.new_password', 'form.re_password']);
    }

    public function test_form_resets_after_successful_change(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Livewire::actingAs($user)
            ->test(EditPassword::class)
            ->set('form.old_password', 'old-password')
            ->set('form.new_password', 'new-password-123')
            ->set('form.re_password', 'new-password-123')
            ->call('submit')
            ->assertSet('form.old_password', '')
            ->assertSet('form.new_password', '')
            ->assertSet('form.re_password', '');
    }
}
