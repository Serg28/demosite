<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_contains_livewire_component(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSeeLivewire(Register::class);
    }

    public function test_user_can_register_with_valid_data(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'existing@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    public function test_register_validates_required_fields(): void
    {
        Livewire::test(Register::class)
            ->call('register')
            ->assertHasErrors(['name', 'email', 'password']);
    }

    public function test_register_validates_password_confirmation(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different')
            ->call('register')
            ->assertHasErrors(['password']);
    }

    public function test_register_validates_minimum_password_length(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Test')
            ->set('email', 'test@example.com')
            ->set('password', '1234567')
            ->set('password_confirmation', '1234567')
            ->call('register')
            ->assertHasErrors(['password']);
    }
}
