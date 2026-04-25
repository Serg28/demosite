<?php

namespace Database\Factories;

use App\Models\Characteristic;
use Illuminate\Database\Eloquent\Factories\Factory;

class CharacteristicFactory extends Factory
{
    protected $model = Characteristic::class;

    public function definition(): array
    {
        return [
            'title' => json_encode([
                'ua' => $this->faker->words(2, true),
                'ru' => $this->faker->words(2, true),
                'en' => $this->faker->words(2, true),
            ]),
            'slug' => $this->faker->unique()->slug(),
            'is_range_type' => $this->faker->boolean(20),
            'is_active' => true,
            'priority' => $this->faker->numberBetween(0, 100),
        ];
    }
}
