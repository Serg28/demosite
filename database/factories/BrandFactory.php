<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => [
                'ua' => $this->faker->company(),
                'ru' => $this->faker->company(),
            ],
            'description' => [
                'ua' => $this->faker->paragraph(),
                'ru' => $this->faker->paragraph(),
            ],
            'picture' => null,
            'priority' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
            'external_id' => null,
        ];
    }
}
