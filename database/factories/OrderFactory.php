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
            'name'           => $this->faker->name(),
            'phone'          => $this->faker->phoneNumber(),
            'email'          => $this->faker->email(),
            'address'        => $this->faker->address(),
            'comment'        => $this->faker->sentence(),
            'cost'           => $this->faker->numberBetween(1000, 100000) / 100,
            'status'         => 'pending',
            'order_status_id'=> 1,
            'delivery'       => $this->faker->randomElement(['nova_poshta', 'justin', 'meest']),
            'pay_method'     => $this->faker->randomElement(['liqpay', 'liqpay_cod']),
            'external_id'    => null,
        ];
    }

    /** Замовлення в статусі "Скасовано клієнтом" */
    public function cancelled(): static
    {
        return $this->state(fn () => ['order_status_id' => 12]);
    }

    /** Замовлення "Доставлено" */
    public function delivered(): static
    {
        return $this->state(fn () => ['order_status_id' => 9]);
    }

    /** З онлайн-методом оплати (LiqPay) */
    public function withOnlinePayMethod(): static
    {
        return $this->state(fn () => [
            'pay_method_id' => \App\Models\PayMethod::where('gateway', 'liqpay')->value('id'),
        ]);
    }
}
