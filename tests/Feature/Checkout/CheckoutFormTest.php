<?php

namespace Tests\Feature\Checkout;

use App\Livewire\Checkout\CheckoutForm;
use App\Livewire\Checkout\DeliverySelector;
use App\Livewire\Checkout\PayPartsCalculator;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\PayMethod;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Linecore\Shoppingcart\Facades\Cart;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutFormTest extends TestCase
{
    use RefreshDatabase;

    protected Delivery $delivery;
    protected PayMethod $payMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->delivery = Delivery::create([
            'title'     => json_encode(['ua' => 'Нова Пошта', 'ru' => 'Новая Почта']),
            'slug'      => 'np_branch',
            'price'     => 95.00,
            'free_cost' => 1500.00,
            'priority'  => 10,
            'is_active' => true,
        ]);

        $this->payMethod = PayMethod::create([
            'title'              => json_encode(['ua' => 'LiqPay', 'ru' => 'LiqPay']),
            'slug'               => 'liqpay',
            'gateway'            => 'liqpay',
            'commission_percent' => 2.75,
            'priority'           => 10,
            'is_active'          => true,
        ]);

        $this->delivery->payments()->attach($this->payMethod->id);
    }

    protected function tearDown(): void
    {
        Cart::destroy();
        parent::tearDown();
    }

    public function test_checkout_page_redirects_when_cart_is_empty(): void
    {
        $this->get(route('checkout.index'))
            ->assertRedirect(route('home'));
    }

    public function test_checkout_page_renders_when_cart_has_items(): void
    {
        Cart::add('1', 'Test Product', 1, 100.00);

        $this->get(route('checkout.index'))
            ->assertOk()
            ->assertSeeLivewire(CheckoutForm::class);
    }

    public function test_checkout_form_mounts_with_delivery_preselected(): void
    {
        Cart::add('1', 'Test Product', 1, 100.00);

        Livewire::test(CheckoutForm::class)
            ->assertSet('deliveryId', $this->delivery->id)
            ->assertSet('payMethodId', $this->payMethod->id);
    }

    public function test_checkout_form_prefills_auth_user_data(): void
    {
        $user = User::factory()->create(['name' => 'Іван Петров', 'email' => 'ivan@example.com']);
        Cart::add('1', 'Test Product', 1, 100.00);

        Livewire::actingAs($user)
            ->test(CheckoutForm::class)
            ->assertSet('firstName', 'Іван')
            ->assertSet('lastName', 'Петров')
            ->assertSet('email', 'ivan@example.com');
    }

    public function test_checkout_form_validates_required_fields(): void
    {
        Cart::add('1', 'Test Product', 1, 100.00);

        Livewire::test(CheckoutForm::class)
            ->set('firstName', '')
            ->set('phone', '')
            ->call('placeOrder')
            ->assertHasErrors(['firstName', 'phone']);
    }

    public function test_checkout_form_validates_delivery_and_payment_required(): void
    {
        Cart::add('1', 'Test Product', 1, 100.00);

        Livewire::test(CheckoutForm::class)
            ->set('firstName', 'Іван')
            ->set('phone', '+380979408386')
            ->set('deliveryId', null)
            ->set('payMethodId', null)
            ->call('placeOrder')
            ->assertHasErrors(['deliveryId', 'payMethodId']);
    }

    public function test_checkout_form_applies_promo_code(): void
    {
        Cart::add('1', 'Test Product', 1, 500.00);

        PromoCode::create([
            'code'      => 'DEMO10',
            'type'      => 'percent',
            'value'     => 10,
            'is_active' => true,
        ]);

        Livewire::test(CheckoutForm::class)
            ->set('promoCodeInput', 'DEMO10')
            ->call('applyPromoCode')
            ->assertSet('promoApplied', true)
            ->assertSet('promoDiscount', 50.00);
    }

    public function test_checkout_form_rejects_invalid_promo_code(): void
    {
        Cart::add('1', 'Test Product', 1, 500.00);

        Livewire::test(CheckoutForm::class)
            ->set('promoCodeInput', 'INVALID')
            ->call('applyPromoCode')
            ->assertSet('promoApplied', false)
            ->assertSet('promoDiscount', 0.0);
    }

    public function test_checkout_form_removes_promo_code(): void
    {
        Cart::add('1', 'Test Product', 1, 500.00);

        PromoCode::create([
            'code'      => 'TEST20',
            'type'      => 'fixed',
            'value'     => 100,
            'is_active' => true,
        ]);

        Livewire::test(CheckoutForm::class)
            ->set('promoCodeInput', 'TEST20')
            ->call('applyPromoCode')
            ->assertSet('promoApplied', true)
            ->call('removePromoCode')
            ->assertSet('promoApplied', false)
            ->assertSet('promoDiscount', 0.0)
            ->assertSet('promoCodeInput', '');
    }

    public function test_checkout_form_resets_pay_method_when_delivery_changes(): void
    {
        $delivery2 = Delivery::create([
            'title'     => json_encode(['ua' => 'Самовивіз', 'ru' => 'Самовывоз']),
            'slug'      => 'pickup',
            'price'     => 0,
            'priority'  => 50,
            'is_active' => true,
        ]);
        // Не прив'язуємо $this->payMethod до $delivery2 — перевірка скидання

        Cart::add('1', 'Test Product', 1, 100.00);

        Livewire::test(CheckoutForm::class)
            ->assertSet('payMethodId', $this->payMethod->id)
            ->set('deliveryId', $delivery2->id)
            ->assertSet('payMethodId', null);
    }

    public function test_checkout_form_notifies_error_when_cart_empty(): void
    {
        Livewire::test(CheckoutForm::class)
            ->set('firstName', 'Іван')
            ->set('phone', '+380979408386')
            ->set('deliveryId', $this->delivery->id)
            ->set('payMethodId', $this->payMethod->id)
            ->call('placeOrder')
            ->assertDispatched('notify');
    }

    public function test_delivery_selector_dispatches_event_on_city_select(): void
    {
        Livewire::test(DeliverySelector::class, [
            'deliveryId'   => $this->delivery->id,
            'deliverySlug' => 'np_branch',
        ])
            ->call('selectCity', 1, 'Київ')
            ->assertDispatched('delivery-details-updated');
    }

    public function test_pay_parts_calculator_returns_empty_for_unknown_gateway(): void
    {
        Livewire::test(PayPartsCalculator::class, [
            'gatewaySlug' => 'unknown_gateway',
            'orderAmount' => 1000.0,
        ])
            ->assertSet('installments', []);
    }

    public function test_checkout_success_page_requires_session(): void
    {
        $order = Order::create([
            'name'    => 'Test',
            'phone'   => '+380971234567',
            'email'   => 'test@example.com',
            'address' => '',
            'cost'    => 100,
            'status'  => 'new',
        ]);

        $this->get(route('checkout.success', $order))
            ->assertForbidden();
    }

    public function test_checkout_success_page_shows_order_with_valid_session(): void
    {
        $order = Order::create([
            'name'    => 'Test',
            'phone'   => '+380971234567',
            'email'   => 'test@example.com',
            'address' => '',
            'cost'    => 500,
            'status'  => 'new',
        ]);

        session(['checkout_order_id' => $order->id]);

        $this->get(route('checkout.success', $order))
            ->assertOk()
            ->assertSee($order->id);
    }
}
