<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\PromoCode;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    public function testSearchCities(): void
    {
        $response = $this->post(route('checkout.search.cities'), [
            'q' => 'Хмельн',
        ]);

        $response->assertStatus(200);
    }

    public function testDeliveryPickupPointers(): void
    {
        $response = $this->post(route('checkout.delivery_pickup_pointers'));

        $response->assertStatus(200);
    }

    public function testSearchDelivery(): void
    {
        $response = $this->post(route('checkout.search.delivery'), [
            'city' => 8629, //Киев
        ]);

        $response->assertStatus(200);
    }

    public function testDeliveryNpPointers(): void
    {
        $response = $this->post(route('checkout.delivery_np_pointers'), [
            'city' => 8629, //Киев
            'type' => 'meest',
        ]);

        $response->assertStatus(200);
    }

    public function testCheckPromoCodeError()
    {
        $response = $this->post(route('checkout.check_promo_code'), [
            'code' => 'asdasd',
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'error']);
    }

    public function testCheckPromoCodeSuccess()
    {
        $promocode = 'promocode';

        $promocodeRecord = PromoCode::create([
            'code' => $promocode,
            'sale' => 5,
            'type' => 'reusable',
            'is_active' => 1,
            'date_exp' => '2025-01-18',
        ]);

        $response = $this->post(route('checkout.check_promo_code'), [
            'code' => $promocode,
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'success']);

        $promocodeRecord->delete();
    }

    public function testGetProducts()
    {
        $response = $this->post(route('checkout.get_products'));
        $response->assertStatus(200);
    }

    public function testCheckoutPage()
    {
        $product = Product::first();
        $product2 = Product::latest()->first();
        $this->post(route('basket.add', $product));
        $this->post(route('basket.add', $product2));

        $response = $this->get(route('checkout'));
        $response->assertStatus(200);
    }

    public function testSendCheckout()
    {
        $product = Product::first();
        $product2 = Product::latest()->first();
        $this->post(route('basket.add', $product));
        $this->post(route('basket.add', $product2));

        $email = $this->faker->email();
        $name = $this->faker->name();
        $phoneNumber = $this->faker->phoneNumber();

        $response = $this->post(route('checkout.send'), [
            'name' => $name,
            'email' => $email,
            'phone' => $phoneNumber,
            'delivery_id' => 1,
            'pay_method_id' => 1,
            'g-recaptcha-response' => 'g-recaptcha-response',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('orders',
            [
                'email' => $email,
                'name' => $name,
                'phone' => $phoneNumber,
            ]
        );

        Order::latest()->first()->delete();
    }
}
