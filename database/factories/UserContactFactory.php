<?php

namespace Database\Factories;

use App\Models\UserContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserContact>
 */
class UserContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'    => \App\Models\User::factory(),
            'type'       => $this->faker->randomElement(['address', 'recipient']),
            'label'      => $this->faker->randomElement(['Дім', 'Робота', null]),
            'first_name' => $this->faker->firstName(),
            'last_name'  => $this->faker->lastName(),
            'phone'      => '+380' . $this->faker->numerify('#########'),
            'email'      => $this->faker->optional()->safeEmail(),
            'is_primary' => false,
            'info'       => null,
        ];
    }

    public function address(): static
    {
        return $this->state(fn () => [
            'type' => 'address',
            'info' => [
                'city'    => $this->faker->city(),
                'address' => $this->faker->streetAddress(),
            ],
        ]);
    }

    public function recipient(): static
    {
        return $this->state(fn () => [
            'type' => 'recipient',
        ]);
    }

    public function primary(): static
    {
        return $this->state(fn () => ['is_primary' => true]);
    }
}
