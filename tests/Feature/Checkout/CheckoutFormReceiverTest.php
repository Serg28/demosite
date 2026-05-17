<?php

namespace Tests\Feature\Checkout;

use App\Livewire\Checkout\CheckoutForm;
use App\Models\City;
use App\Models\Delivery;
use App\Models\PayMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Linecore\Shoppingcart\Facades\Cart;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutFormReceiverTest extends TestCase
{
    use RefreshDatabase;

    protected Delivery $delivery;

    protected PayMethod $payMethod;

    protected City $city;

    protected function setUp(): void
    {
        parent::setUp();

        $this->city = City::create([
            'title' => json_encode(['ua' => 'Київ', 'ru' => 'Киев']),
            'is_active' => true,
        ]);

        $this->delivery = Delivery::create([
            'title' => json_encode(['ua' => 'Нова Пошта', 'ru' => 'Новая Почта']),
            'slug' => 'np_branch',
            'price' => 95.00,
            'free_cost' => 1500.00,
            'is_for_all_cities' => true,
            'priority' => 10,
            'is_active' => true,
        ]);

        $this->payMethod = PayMethod::create([
            'title' => json_encode(['ua' => 'LiqPay', 'ru' => 'LiqPay']),
            'slug' => 'liqpay',
            'gateway' => 'liqpay',
            'commission_percent' => 2.75,
            'priority' => 10,
            'is_active' => true,
        ]);

        $this->delivery->payments()->attach($this->payMethod->id);
    }

    protected function tearDown(): void
    {
        Cart::destroy();
        parent::tearDown();
    }

    public function test_receiver_defaults_to_user(): void
    {
        Livewire::test(CheckoutForm::class)
            ->assertSet('receiver', 'user');
    }

    public function test_call_me_defaults_to_false(): void
    {
        Livewire::test(CheckoutForm::class)
            ->assertSet('callMe', false);
    }

    public function test_receiver_can_be_switched_to_other(): void
    {
        Livewire::test(CheckoutForm::class)
            ->set('receiver', 'other')
            ->assertSet('receiver', 'other');
    }

    public function test_receiver_other_requires_first_name_and_phone(): void
    {
        Cart::add('1', 'Test Product', 1, 500.00);

        Livewire::test(CheckoutForm::class)
            ->set('firstName', 'Іван')
            ->set('phone', '+380979408386')
            ->set('cityId', $this->city->id)
            ->set('deliveryId', $this->delivery->id)
            ->set('deliveryWarehouseId', 1)
            ->set('payMethodId', $this->payMethod->id)
            ->set('receiver', 'other')
            ->set('receiverFirstName', '')
            ->set('receiverPhone', '')
            ->call('placeOrder')
            ->assertHasErrors(['receiverFirstName', 'receiverPhone']);
    }

    public function test_receiver_user_does_not_require_receiver_fields(): void
    {
        Cart::add('1', 'Test Product', 1, 500.00);

        Livewire::test(CheckoutForm::class)
            ->set('firstName', 'Іван')
            ->set('phone', '+380979408386')
            ->set('cityId', $this->city->id)
            ->set('deliveryId', $this->delivery->id)
            ->set('deliveryWarehouseId', 1)
            ->set('payMethodId', $this->payMethod->id)
            ->set('receiver', 'user')
            ->set('receiverFirstName', '')
            ->set('receiverPhone', '')
            ->call('placeOrder')
            ->assertHasNoErrors(['receiverFirstName', 'receiverPhone']);
    }

    public function test_receiver_other_accepts_valid_fields(): void
    {
        Livewire::test(CheckoutForm::class)
            ->set('receiver', 'other')
            ->set('receiverFirstName', 'Марія')
            ->set('receiverLastName', 'Коваль')
            ->set('receiverPatronymic', 'Іванівна')
            ->set('receiverPhone', '+380991234567')
            ->set('receiverEmail', 'maria@example.com')
            ->assertSet('receiver', 'other')
            ->assertSet('receiverFirstName', 'Марія')
            ->assertSet('receiverPhone', '+380991234567');
    }

    public function test_call_me_can_be_set_to_true(): void
    {
        Livewire::test(CheckoutForm::class)
            ->set('callMe', true)
            ->assertSet('callMe', true);
    }

    public function test_receiver_invalid_value_fails_validation(): void
    {
        Cart::add('1', 'Test Product', 1, 500.00);

        Livewire::test(CheckoutForm::class)
            ->set('firstName', 'Іван')
            ->set('phone', '+380979408386')
            ->set('cityId', $this->city->id)
            ->set('deliveryId', $this->delivery->id)
            ->set('deliveryWarehouseId', 1)
            ->set('payMethodId', $this->payMethod->id)
            ->set('receiver', 'invalid_value')
            ->call('placeOrder')
            ->assertHasErrors(['receiver']);
    }
}
