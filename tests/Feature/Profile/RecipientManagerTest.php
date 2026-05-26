<?php

namespace Tests\Feature\Profile;

use App\Livewire\Profile\Recipient\Manager;
use App\Models\User;
use App\Models\UserContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RecipientManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipients_page_requires_auth(): void
    {
        $this->get(route('profile.recipients'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_recipients_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.recipients'))
            ->assertOk()
            ->assertSeeLivewire(Manager::class);
    }

    public function test_component_renders_empty_state(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->assertSee('Отримувачів ще немає');
    }

    public function test_component_renders_user_recipients(): void
    {
        $user = User::factory()->create();
        UserContact::factory()->recipient()->create([
            'user_id'    => $user->id,
            'first_name' => 'Іван',
            'last_name'  => 'Петренко',
            'phone'      => '+380501234567',
        ]);

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->assertSee('Іван')
            ->assertSee('Петренко');
    }

    public function test_user_can_add_recipient(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->set('showForm', true)
            ->set('form.first_name', 'Марія')
            ->set('form.last_name', 'Коваль')
            ->set('form.phone', '+380671234567')
            ->call('add')
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('user_contacts', [
            'user_id'    => $user->id,
            'type'       => 'recipient',
            'first_name' => 'Марія',
            'last_name'  => 'Коваль',
        ]);
    }

    public function test_add_recipient_validates_required_fields(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->set('showForm', true)
            ->call('add')
            ->assertHasErrors(['form.first_name', 'form.last_name', 'form.phone']);
    }

    public function test_add_recipient_validates_email_format(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->set('showForm', true)
            ->set('form.first_name', 'Тест')
            ->set('form.last_name', 'Тест')
            ->set('form.phone', '+380671234567')
            ->set('form.email', 'not-an-email')
            ->call('add')
            ->assertHasErrors(['form.email']);
    }

    public function test_user_can_delete_recipient(): void
    {
        $user = User::factory()->create();
        $recipient = UserContact::factory()->recipient()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->call('delete', $recipient->id);

        $this->assertDatabaseMissing('user_contacts', ['id' => $recipient->id]);
    }

    public function test_user_cannot_delete_other_users_recipient(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $recipient = UserContact::factory()->recipient()->create(['user_id' => $other->id]);

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->call('delete', $recipient->id);

        $this->assertDatabaseHas('user_contacts', ['id' => $recipient->id]);
    }

    public function test_user_can_set_primary_recipient(): void
    {
        $user = User::factory()->create();
        $rec1 = UserContact::factory()->recipient()->primary()->create(['user_id' => $user->id]);
        $rec2 = UserContact::factory()->recipient()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->call('setPrimary', $rec2->id);

        $this->assertDatabaseHas('user_contacts', ['id' => $rec2->id, 'is_primary' => 1]);
        $this->assertDatabaseHas('user_contacts', ['id' => $rec1->id, 'is_primary' => 0]);
    }

    public function test_cancel_form_resets_state(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->set('showForm', true)
            ->set('form.first_name', 'Тест')
            ->call('cancelForm')
            ->assertSet('showForm', false)
            ->assertSet('form.first_name', '');
    }
}
