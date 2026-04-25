<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'address' => $this->faker->address(),
            'comment' => $this->faker->sentence(),
            'cost' => $this->faker->numberBetween(1000, 100000) / 100,
            'status' => 'pending',
            'delivery' => $this->faker->randomElement(['nova_poshta', 'justin', 'meest']),
            'pay_method' => $this->faker->randomElement(['liqpay', 'monopay', 'wayfopay']),
            'external_id' => null,
        ];
    }
}
