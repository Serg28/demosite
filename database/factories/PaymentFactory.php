<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => \App\Models\Order::factory(),
            'amount' => $this->faker->numberBetween(1000, 50000) / 100,
            'status' => 'pending',
            'gateway' => $this->faker->randomElement(['liqpay', 'monopay', 'wayfopay']),
            'transaction_id' => $this->faker->unique()->regexify('[A-Z0-9]{20}'),
            'response' => null,
        ];
    }
}
