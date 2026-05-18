<?php

namespace Tests\Feature\Checkout;

use App\Livewire\Checkout\DeliverySelector;
use App\Models\City;
use App\Models\Delivery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NpAddressDeliveryTest extends TestCase
{
    use RefreshDatabase;

    private Delivery $delivery;

    private City $city;

    protected function setUp(): void
    {
        parent::setUp();

        $this->city = City::create([
            'title'     => json_encode(['ua' => 'Київ', 'ru' => 'Киев']),
            'is_active' => true,
        ]);

        $this->delivery = Delivery::create([
            'title'             => json_encode(['ua' => 'НП адресна', 'ru' => 'НП адресная']),
            'slug'              => 'np_address',
            'price'             => 120.00,
            'is_for_all_cities' => true,
            'is_active'         => true,
        ]);
    }

    public function test_np_address_config_has_required_and_optional_fields(): void
    {
        $fields = config('checkout.delivery_fields.np_address');

        $this->assertNotNull($fields);
        $this->assertContains('street', $fields['required']);
        $this->assertContains('house', $fields['required']);
        $this->assertContains('apartment', $fields['optional']);
        $this->assertContains('building', $fields['optional']);
        $this->assertContains('floor', $fields['optional']);
    }

    public function test_np_address_does_not_include_elevator_or_lifting(): void
    {
        $fields = config('checkout.delivery_fields.np_address');
        $all    = array_merge($fields['required'], $fields['optional']);

        $this->assertNotContains('isElevator', $all);
        $this->assertNotContains('isLifting', $all);
    }

    public function test_np_address_blade_view_exists(): void
    {
        $this->assertTrue(
            view()->exists('livewire.checkout.delivery.np_address'),
            'np_address blade view must exist'
        );
    }

    public function test_delivery_selector_renders_np_address_form(): void
    {
        Livewire::test(DeliverySelector::class, [
            'deliveryId'   => $this->delivery->id,
            'deliverySlug' => 'np_address',
            'cityId'       => $this->city->id,
        ])
            ->assertViewIs('livewire.checkout.delivery-selector')
            ->assertSee('Вулиця');
    }
}
