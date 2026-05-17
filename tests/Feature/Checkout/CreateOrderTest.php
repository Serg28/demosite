<?php

namespace Tests\Feature\Checkout;

use App\DTO\Checkout\CheckoutContext;
use App\DTO\Checkout\DiscountBreakdown;
use App\DTO\Checkout\TaxBreakdown;
use App\Models\City;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\PayMethod;
use App\Models\Product;
use App\PipelineSteps\Checkout\CreateOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Linecore\Shoppingcart\CartItem;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    private Delivery $delivery;

    private PayMethod $payMethod;

    private City $city;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create();

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
    }

    private function makeContext(array $overrides = []): CheckoutContext
    {
        $context = new CheckoutContext;
        $context->firstName = 'Іван';
        $context->lastName = 'Петренко';
        $context->phone = '+380971234567';
        $context->email = 'ivan@example.com';
        $context->comment = '';
        $context->callMe = false;
        $context->receiver = 'user';
        $context->receiverFirstName = '';
        $context->receiverLastName = '';
        $context->receiverPatronymic = '';
        $context->receiverPhone = '';
        $context->receiverEmail = '';
        $context->cityId = $this->city->id;
        $context->delivery = $this->delivery;
        $context->payMethod = $this->payMethod;
        $context->deliveryWarehouseId = null;
        $context->deliveryPickupPointId = null;
        $context->address = '';
        $context->subtotal = 500.00;
        $context->discountTotal = 0.0;
        $context->deliveryPrice = 95.00;
        $context->total = 595.00;
        $context->paymentAmount = 595.00;
        $context->discountBreakdown = DiscountBreakdown::empty();
        $context->taxes = new TaxBreakdown(productTax: 0.0, paymentCommission: 0.0);

        $item = new CartItem($this->product->id, 'Товар тестовий', 500.00);
        $item->qty = 1;
        $context->cartItems = collect([$item]);

        foreach ($overrides as $key => $value) {
            $context->$key = $value;
        }

        return $context;
    }

    private function runStep(CheckoutContext $context): CheckoutContext
    {
        return (new CreateOrder)->handle($context, fn ($ctx) => $ctx);
    }

    public function test_order_is_created_with_basic_customer_fields(): void
    {
        $context = $this->makeContext();
        $result = $this->runStep($context);

        $this->assertNotNull($result->order);

        $order = Order::findOrFail($result->order->id);
        $this->assertEquals('Іван', $order->first_name);
        $this->assertEquals('Петренко', $order->last_name);
        $this->assertEquals('+380971234567', $order->phone);
        $this->assertEquals('ivan@example.com', $order->email);
    }

    public function test_call_me_true_is_saved_in_order(): void
    {
        $context = $this->makeContext(['callMe' => true]);
        $result = $this->runStep($context);

        $order = Order::findOrFail($result->order->id);
        $this->assertTrue((bool) $order->call_me);
    }

    public function test_call_me_false_is_saved_in_order(): void
    {
        $context = $this->makeContext(['callMe' => false]);
        $result = $this->runStep($context);

        $order = Order::findOrFail($result->order->id);
        $this->assertFalse((bool) $order->call_me);
    }

    public function test_receiver_user_leaves_receiver_fields_null(): void
    {
        $context = $this->makeContext([
            'receiver' => 'user',
            'receiverFirstName' => 'Марія',
            'receiverPhone' => '+380991234567',
        ]);
        $result = $this->runStep($context);

        $order = Order::findOrFail($result->order->id);
        $this->assertNull($order->receiver_first_name);
        $this->assertNull($order->receiver_last_name);
        $this->assertNull($order->receiver_phone);
        $this->assertNull($order->receiver_email);
    }

    public function test_receiver_other_saves_all_receiver_fields(): void
    {
        $context = $this->makeContext([
            'receiver' => 'other',
            'receiverFirstName' => 'Марія',
            'receiverLastName' => 'Коваль',
            'receiverPatronymic' => 'Іванівна',
            'receiverPhone' => '+380991234567',
            'receiverEmail' => 'maria@example.com',
        ]);
        $result = $this->runStep($context);

        $order = Order::findOrFail($result->order->id);
        $this->assertEquals('Марія', $order->receiver_first_name);
        $this->assertEquals('Коваль', $order->receiver_last_name);
        $this->assertEquals('Іванівна', $order->receiver_patronymic);
        $this->assertEquals('+380991234567', $order->receiver_phone);
        $this->assertEquals('maria@example.com', $order->receiver_email);
    }

    public function test_receiver_other_with_partial_fields_saves_only_provided(): void
    {
        $context = $this->makeContext([
            'receiver' => 'other',
            'receiverFirstName' => 'Тарас',
            'receiverLastName' => '',
            'receiverPatronymic' => '',
            'receiverPhone' => '+380631234567',
            'receiverEmail' => '',
        ]);
        $result = $this->runStep($context);

        $order = Order::findOrFail($result->order->id);
        $this->assertEquals('Тарас', $order->receiver_first_name);
        $this->assertEquals('', $order->receiver_last_name);
        $this->assertEquals('+380631234567', $order->receiver_phone);
    }

    public function test_order_product_is_created_for_each_cart_item(): void
    {
        $context = $this->makeContext();
        $result = $this->runStep($context);

        $order = Order::with('products')->findOrFail($result->order->id);
        $this->assertCount(1, $order->products);

        $product = $order->products->first();
        $this->assertEquals($this->product->id, $product->product_id);
        $this->assertEquals('Товар тестовий', $product->title);
        $this->assertEquals(1, $product->count);
        $this->assertEquals(500.00, $product->price);
        $this->assertEquals(500.00, $product->amount);
    }

    public function test_order_totals_are_saved_correctly(): void
    {
        $context = $this->makeContext([
            'total' => 595.00,
            'subtotal' => 500.00,
            'discountTotal' => 0.0,
            'deliveryPrice' => 95.00,
            'taxes' => new TaxBreakdown(productTax: 0.0, paymentCommission: 10.0),
            'discountBreakdown' => new DiscountBreakdown(promoCode: 0.0, discountCard: 0.0),
        ]);
        $result = $this->runStep($context);

        $order = Order::findOrFail($result->order->id);
        $this->assertEquals(595.00, $order->cost);
        $this->assertEquals(500.00, $order->cost_without_sale);
        $this->assertEquals(0.0, $order->sale);
        $this->assertEquals(10.0, $order->payment_commission);
    }

    public function test_order_context_is_populated_after_creation(): void
    {
        $context = $this->makeContext();
        $result = $this->runStep($context);

        $this->assertNotNull($result->order);
        $this->assertInstanceOf(Order::class, $result->order);
        $this->assertGreaterThan(0, $result->order->id);
    }
}
