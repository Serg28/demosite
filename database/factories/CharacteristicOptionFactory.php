<?php

namespace Database\Factories;

use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class CharacteristicOptionFactory extends Factory
{
    protected $model = CharacteristicOption::class;

    public function definition(): array
    {
        return [
            'characteristic_id' => Characteristic::factory(),
            'title' => [
                'ua' => $this->faker->words(2, true),
                'ru' => $this->faker->words(2, true),
                'en' => $this->faker->words(2, true),
            ],
            'slug' => $this->faker->unique()->slug(),
            'value' => $this->faker->numerify('###'),
            'priority' => $this->faker->numberBetween(0, 100),
        ];
    }
}
