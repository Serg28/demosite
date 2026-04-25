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
        $name = $this->faker->company();

        return [
            'title' => [
                'ua' => $name,
                'ru' => $name,
            ],
            'slug' => $this->faker->unique()->slug(),
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
