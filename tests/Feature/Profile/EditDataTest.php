<?php

namespace Tests\Feature\Profile;

use App\Livewire\Profile\User\EditData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_data_component_renders(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Іван',
            'last_name'  => 'Петренко',
            'email'      => 'ivan@example.com',
        ]);

        Livewire::actingAs($user)
            ->test(EditData::class)
            ->assertSee('Іван')
            ->assertSee('Петренко')
            ->assertSet('editMode', false);
    }

    public function test_edit_mode_toggle(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(EditData::class)
            ->assertSet('editMode', false)
            ->call('editProfile')
            ->assertSet('editMode', true)
            ->call('viewProfile')
            ->assertSet('editMode', false);
    }

    public function test_user_can_update_profile_data(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Іван',
            'last_name'  => 'Старе',
            'email'      => 'ivan@example.com',
        ]);

        Livewire::actingAs($user)
            ->test(EditData::class)
            ->set('form.first_name', 'Василь')
            ->set('form.last_name', 'Новий')
            ->set('form.email', 'vasyl@example.com')
            ->call('submit')
            ->assertSet('editMode', false)
            ->assertDispatched('profile-updated');

        $user->refresh();
        $this->assertEquals('Василь', $user->first_name);
        $this->assertEquals('Новий', $user->last_name);
        $this->assertEquals('vasyl@example.com', $user->email);
    }

    public function test_validation_fails_with_empty_required_fields(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(EditData::class)
            ->set('form.first_name', '')
            ->set('form.last_name', '')
            ->set('form.email', '')
            ->call('submit')
            ->assertHasErrors(['form.first_name', 'form.last_name', 'form.email']);
    }

    public function test_validation_fails_with_invalid_email(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(EditData::class)
            ->set('form.first_name', 'Василь')
            ->set('form.last_name', 'Іваненко')
            ->set('form.email', 'not-an-email')
            ->call('submit')
            ->assertHasErrors(['form.email']);
    }

    public function test_optional_fields_can_be_empty(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Іван',
            'last_name'  => 'Петренко',
            'email'      => 'ivan@example.com',
        ]);

        Livewire::actingAs($user)
            ->test(EditData::class)
            ->set('form.patronymic', '')
            ->set('form.phone', '')
            ->call('submit')
            ->assertHasNoErrors();
    }
}
