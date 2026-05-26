<?php

namespace Database\Factories;

use App\Models\PaymentInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentInvoice>
 */
class PaymentInvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id'         => null,
            'pay_method_id'    => null,
            'amount'           => $this->faker->randomFloat(2, 100, 5000),
            'status'           => 'pending',
            'gateway_ref'      => null,
            'payment_url'      => null,
            'gateway_response' => null,
            'paid_at'          => null,
            'fail_reason'      => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending', 'payment_url' => null]);
    }

    public function initiated(): static
    {
        return $this->state(fn () => [
            'status'      => 'initiated',
            'payment_url' => 'https://payment.example.com/pay/' . fake()->uuid(),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status'  => 'paid',
            'paid_at' => now(),
        ]);
    }
}
