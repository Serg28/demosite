<?php

namespace Database\Factories;

use App\Models\PromoCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromoCode>
 */
class PromoCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code'                   => strtoupper($this->faker->bothify('PROMO-####')),
            'type'                   => 'percent',
            'value'                  => $this->faker->randomElement([5, 10, 15, 20]),
            'is_active'              => true,
            'usage_type'             => 'reusable',
            'is_used'                => false,
            'used_count'             => 0,
            'use_for_installments'   => true,
            'use_for_promotional'    => true,
            'use_for_discount_cards' => true,
        ];
    }
}
