<?php

namespace Tests\Feature\Profile;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentInvoice;
use App\Models\PayMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderActionsTest extends TestCase
{
    use RefreshDatabase;

    private PayMethod $onlineMethod;

    private PayMethod $codMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->onlineMethod = PayMethod::create([
            'slug'               => 'liqpay',
            'gateway'            => 'liqpay',
            'title'              => json_encode(['uk' => 'Картка онлайн']),
            'commission_percent' => 0,
            'priority'           => 10,
            'is_active'          => true,
            'use_hold'           => false,
        ]);

        $this->codMethod = PayMethod::create([
            'slug'               => 'liqpay_cod',
            'gateway'            => 'liqpay_cod',
            'title'              => json_encode(['uk' => 'Накладений платіж']),
            'commission_percent' => 0,
            'priority'           => 40,
            'is_active'          => true,
            'use_hold'           => false,
        ]);
    }

    // ─────────────────────────── CancelOrder ───────────────────────────

    public function test_user_can_cancel_new_order_via_modal(): void
    {
        $user  = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'order_status_id' => 1]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Order\CancelOrder::class, ['id' => $order->id])
            ->call('confirm')
            ->assertSet('state', 'success');

        $this->assertDatabaseHas('orders', [
            'id'              => $order->id,
            'order_status_id' => 12,
        ]);
    }

    public function test_user_cannot_cancel_already_cancelled_order(): void
    {
        $user  = User::factory()->create();
        $order = Order::factory()->cancelled()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Order\CancelOrder::class, ['id' => $order->id])
            ->call('confirm')
            ->assertSet('state', 'error');
    }

    public function test_user_cannot_cancel_another_users_order(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $other->id, 'order_status_id' => 1]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Order\CancelOrder::class, ['id' => $order->id])
            ->call('confirm')
            ->assertSet('state', 'error');
    }

    public function test_cancel_order_route_requires_authentication(): void
    {
        $order = Order::factory()->create(['order_status_id' => 1]);

        $this->post(route('profile.orders.repeat', $order->id))
            ->assertRedirect();
    }

    // ─────────────────────────── RepeatOrder (Livewire modal) ───────────────────────────

    public function test_repeat_order_modal_mounts_with_products(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 500]);
        $order   = Order::factory()->create(['user_id' => $user->id]);

        OrderProduct::factory()->create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'title'      => 'Test Product',
            'price'      => 500,
            'count'      => 2,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Order\RepeatOrder::class, ['id' => $order->id])
            ->assertSet('orderId', $order->id)
            ->assertCount('items', 1)
            ->assertSee('Test Product')
            ->assertSee('data-js-add-all-to-cart', false);
    }

    public function test_repeat_order_modal_shows_empty_state_when_no_products(): void
    {
        $user  = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Order\RepeatOrder::class, ['id' => $order->id])
            ->assertCount('items', 0);
    }

    public function test_repeat_order_modal_items_contain_correct_data(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 500]);
        $order   = Order::factory()->create(['user_id' => $user->id]);

        OrderProduct::factory()->create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'title'      => 'Test Product',
            'price'      => 299,
            'count'      => 3,
        ]);

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Order\RepeatOrder::class, ['id' => $order->id]);

        $items = $component->get('items');
        $this->assertCount(1, $items);
        $this->assertSame($product->id, $items[0]['id']);
        $this->assertSame(3, $items[0]['count']);
        $this->assertSame(299.0, $items[0]['price']);
    }

    // ─────────────────────────── RepeatOrder (route) ───────────────────────────

    public function test_user_can_repeat_order(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 500]);
        $order   = Order::factory()->create(['user_id' => $user->id]);

        OrderProduct::factory()->create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'title'      => 'Test Product',
            'price'      => 500,
            'count'      => 2,
        ]);

        $this->actingAs($user)
            ->post(route('profile.orders.repeat', $order->id))
            ->assertRedirect(route('profile.orders.details', $order->id))
            ->assertSessionHas('success');
    }

    public function test_repeat_order_redirects_guest_to_login(): void
    {
        $order = Order::factory()->create();

        $this->post(route('profile.orders.repeat', $order->id))
            ->assertRedirect();
    }

    public function test_user_cannot_repeat_another_users_order(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $other->id]);

        // Order scope filters by user → 404 (not enumerable by design)
        $this->actingAs($user)
            ->post(route('profile.orders.repeat', $order->id))
            ->assertNotFound();
    }

    // ─────────────────────────── PayOrder ───────────────────────────

    public function test_pay_order_redirects_to_payment_url_when_invoice_exists(): void
    {
        $user  = User::factory()->create();
        $order = Order::factory()->create([
            'user_id'         => $user->id,
            'order_status_id' => 1,
            'pay_method_id'   => $this->onlineMethod->id,
        ]);

        PaymentInvoice::factory()->initiated()->create([
            'order_id'      => $order->id,
            'pay_method_id' => $this->onlineMethod->id,
        ]);

        $this->actingAs($user)
            ->post(route('profile.orders.pay', $order->id))
            ->assertRedirect();
    }

    public function test_pay_order_fails_for_cod_method(): void
    {
        $user  = User::factory()->create();
        $order = Order::factory()->create([
            'user_id'         => $user->id,
            'order_status_id' => 1,
            'pay_method_id'   => $this->codMethod->id,
        ]);

        $this->actingAs($user)
            ->post(route('profile.orders.pay', $order->id))
            ->assertRedirect(route('profile.orders.details', $order->id))
            ->assertSessionHas('error');
    }

    public function test_pay_order_fails_for_cancelled_order(): void
    {
        $user  = User::factory()->create();
        $order = Order::factory()->cancelled()->create([
            'user_id'       => $user->id,
            'pay_method_id' => $this->onlineMethod->id,
        ]);

        $this->actingAs($user)
            ->post(route('profile.orders.pay', $order->id))
            ->assertRedirect(route('profile.orders.details', $order->id))
            ->assertSessionHas('error');
    }

    // ─────────────────────────── Order model helpers ───────────────────────────

    public function test_order_can_be_cancelled_when_new(): void
    {
        $order = Order::factory()->make(['order_status_id' => 1]);
        $this->assertTrue($order->canBeCancelled());
    }

    public function test_order_cannot_be_cancelled_when_delivered(): void
    {
        $order = Order::factory()->delivered()->make();
        $this->assertFalse($order->canBeCancelled());
    }

    public function test_order_can_be_repaid_when_new_with_online_pay_method(): void
    {
        $order = Order::factory()->make([
            'order_status_id' => 1,
            'pay_method_id'   => $this->onlineMethod->id,
        ]);
        $order->setRelation('payMethod', $this->onlineMethod);

        $this->assertTrue($order->canBeRepaid());
    }

    public function test_order_cannot_be_repaid_with_cod(): void
    {
        $order = Order::factory()->make([
            'order_status_id' => 1,
            'pay_method_id'   => $this->codMethod->id,
        ]);
        $order->setRelation('payMethod', $this->codMethod);

        $this->assertFalse($order->canBeRepaid());
    }
}
