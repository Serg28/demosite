<?php

namespace Database\Factories;

use App\Models\DiscountCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiscountCard>
 */
class DiscountCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code'      => strtoupper($this->faker->bothify('DC-####??')),
            'type'      => 'percent',
            'value'     => $this->faker->randomElement([5, 10, 15]),
            'phone'     => '+380' . $this->faker->numerify('#########'),
            'is_active' => true,
        ];
    }
}
