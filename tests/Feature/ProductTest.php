<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function testOpenProduct()
    {
        $product = Product::first();

        $response = $this->get($product->getUrl());
        $response->assertStatus(200);
    }

    public function testFollowPrice()
    {
        $product = Product::first();
        $email = $this->faker->email();

        $response = $this->post(route('follow_price', $product), [
            'g_recaptcha_response' => 'g_recaptcha_response',
            'email' => $email,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('follow_prices',
            [
                'product_id' => $product->id,
                'email' => $email,
            ]
        );
    }
}
