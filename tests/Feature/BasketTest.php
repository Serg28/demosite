<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class BasketTest extends TestCase
{
    public function testBuyOneClick()
    {
        $product = Product::first();
        $phone = $this->faker->phoneNumber();

        $response = $this->post(route('order.buy_one_click', $product), [
            'phone' => $phone,
        ]);
        $response->assertStatus(200)->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('orders',
            [
                'phone' => $phone,
            ]
        );
    }

    public function testAddProduct()
    {
        $product = Product::first();
        $response = $this->post(route('basket.add', $product));
        $response->assertStatus(200)->assertJson(['status' => 'success', 'count' => 1]);
    }

    public function testRemoveProduct()
    {
        $product = Product::first();
        $product2 = Product::latest()->first();
        $this->post(route('basket.add', $product));
        $this->post(route('basket.add', $product2));

        $response = $this->post(route('basket.remove', \Cart::content()->first()->rowId));

        $response->assertStatus(200)->assertJson(['status' => 'success', 'count' => 1]);
    }

    public function testUpdateCart()
    {
        $product = Product::first();
        $this->post(route('basket.add', $product));

        $response = $this->post(route('basket.update', \Cart::content()->first()->rowId), [
            'count' => 10,
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'success', 'count' => 10]);
    }
}
