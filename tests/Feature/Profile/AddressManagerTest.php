<?php

namespace Tests\Feature\Profile;

use App\Livewire\Profile\Address\Manager;
use App\Models\User;
use App\Models\UserContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AddressManagerTest extends TestCase
{
    use RefreshDatabase;

    // ── Routing & auth ─────────────────────────────────────────────────

    public function test_addresses_page_requires_auth(): void
    {
        $this->get(route('profile.addresses'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_addresses_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.addresses'))
            ->assertOk()
            ->assertSeeLivewire(Manager::class);
    }

    // ── Rendering ──────────────────────────────────────────────────────

    public function test_component_renders_empty_state(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->assertSee('Адрес поки немає');
    }

    public function test_component_renders_saved_address_with_new_format(): void
    {
        $user = User::factory()->create();
        UserContact::factory()->address()->create([
            'user_id' => $user->id,
            'label'   => 'Дім',
            'info'    => [
                'delivery_slug'   => 'np_branch',
                'city_id'         => 1,
                'city_title'      => 'Київ',
                'warehouse_id'    => 10,
                'warehouse_title' => 'Відділення №1',
            ],
        ]);

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->assertSee('Дім')
            ->assertSee('Київ')
            ->assertSee('Відділення №1');
    }

    public function test_component_renders_saved_address_with_legacy_format(): void
    {
        /** Зворотна сумісність зі старим форматом {city, address} */
        $user = User::factory()->create();
        UserContact::factory()->address()->create([
            'user_id' => $user->id,
            'label'   => 'Старий формат',
            'info'    => ['city' => 'Харків', 'address' => 'вул. Сумська, 1'],
        ]);

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->assertSee('Харків')
            ->assertSee('вул. Сумська, 1');
    }

    // ── Add (warehouse delivery) ────────────────────────────────────────

    public function test_user_can_add_warehouse_address(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->set('showForm', true)
            ->call('selectCity', 1, 'Київ')
            ->call('selectDelivery', 1, 'np_branch')
            ->call('selectWarehouse', 10, 'Відділення №1')
            ->call('add')
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('user_contacts', [
            'user_id' => $user->id,
            'type'    => 'address',
        ]);

        $contact = UserContact::where('user_id', $user->id)->first();
        $this->assertEquals(1, $contact->info['city_id']);
        $this->assertEquals('np_branch', $contact->info['delivery_slug']);
        $this->assertEquals(10, $contact->info['warehouse_id']);
    }

    // ── Add (courier delivery) ──────────────────────────────────────────

    public function test_user_can_add_courier_address(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->set('showForm', true)
            ->call('selectCity', 1, 'Київ')
            ->call('selectDelivery', 3, 'np_address')
            ->set('street', 'вул. Хрещатик')
            ->set('house', '1')
            ->set('apartment', '12')
            ->call('add')
            ->assertSet('showForm', false);

        $contact = UserContact::where('user_id', $user->id)->first();
        $this->assertEquals('вул. Хрещатик', $contact->info['street']);
        $this->assertEquals('1', $contact->info['house']);
    }

    // ── Validation ─────────────────────────────────────────────────────

    public function test_add_validates_city_is_required(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->set('showForm', true)
            ->call('add')
            ->assertHasErrors(['cityId']);
    }

    public function test_add_validates_street_required_for_courier(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->set('showForm', true)
            ->call('selectCity', 1, 'Київ')
            ->call('selectDelivery', 3, 'np_address')
            ->call('add')
            ->assertHasErrors(['street', 'house']);
    }

    // ── Delete ──────────────────────────────────────────────────────────

    public function test_user_can_delete_address(): void
    {
        $user    = User::factory()->create();
        $address = UserContact::factory()->address()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->call('delete', $address->id);

        $this->assertDatabaseMissing('user_contacts', ['id' => $address->id]);
    }

    public function test_user_cannot_delete_other_users_address(): void
    {
        $user    = User::factory()->create();
        $other   = User::factory()->create();
        $address = UserContact::factory()->address()->create(['user_id' => $other->id]);

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->call('delete', $address->id);

        $this->assertDatabaseHas('user_contacts', ['id' => $address->id]);
    }

    // ── SetPrimary ──────────────────────────────────────────────────────

    public function test_user_can_set_primary_address(): void
    {
        $user  = User::factory()->create();
        $addr1 = UserContact::factory()->address()->primary()->create(['user_id' => $user->id]);
        $addr2 = UserContact::factory()->address()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->call('setPrimary', $addr2->id);

        $this->assertDatabaseHas('user_contacts', ['id' => $addr2->id, 'is_primary' => 1]);
        $this->assertDatabaseHas('user_contacts', ['id' => $addr1->id, 'is_primary' => 0]);
    }

    // ── City/delivery state transitions ────────────────────────────────

    public function test_clearing_city_resets_delivery_and_warehouse(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->call('selectCity', 1, 'Київ')
            ->call('selectDelivery', 1, 'np_branch')
            ->call('selectWarehouse', 10, 'Відділення №1')
            ->call('clearCity')
            ->assertSet('cityId', null)
            ->assertSet('deliveryId', null)
            ->assertSet('warehouseId', null);
    }

    public function test_cancel_form_resets_all_state(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Manager::class)
            ->set('showForm', true)
            ->call('selectCity', 1, 'Київ')
            ->call('selectDelivery', 1, 'np_branch')
            ->set('form.label', 'Тест')
            ->call('cancelForm')
            ->assertSet('showForm', false)
            ->assertSet('cityId', null)
            ->assertSet('deliveryId', null)
            ->assertSet('form.label', '');
    }
}
