<?php

namespace Tests\Feature\Checkout;

use App\Livewire\Checkout\CheckoutForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Linecore\Shoppingcart\Facades\Cart;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutEmptyCartTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Cart::destroy();
        parent::tearDown();
    }

    public function test_mount_redirects_to_home_when_cart_is_empty(): void
    {
        Livewire::test(CheckoutForm::class)
            ->assertRedirect(route('home'));
    }

    public function test_remove_last_item_redirects_to_home(): void
    {
        Cart::add('p1', 'Product One', 1, 100.00);
        $rowId = Cart::content()->first()->rowId;

        Livewire::test(CheckoutForm::class)
            ->call('removeCartItem', $rowId)
            ->assertRedirect(route('home'));
    }

    public function test_update_qty_to_zero_on_last_item_redirects_to_home(): void
    {
        Cart::add('p1', 'Product One', 1, 100.00);
        $rowId = Cart::content()->first()->rowId;

        Livewire::test(CheckoutForm::class)
            ->call('updateCartQty', $rowId, 0)
            ->assertRedirect(route('home'));
    }

    public function test_placeorder_server_guard_catches_empty_cart(): void
    {
        // Safety-net test: bypass mount by adding item, then empty the cart before placeOrder
        Cart::add('p1', 'Product One', 1, 100.00);

        $component = Livewire::test(CheckoutForm::class);
        $component->assertSet('cartCount', 1);

        Cart::destroy();

        $component->call('placeOrder')
            ->assertDispatched('notify');
    }
}
