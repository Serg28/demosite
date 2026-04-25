<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
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
                'ua' => $this->faker->words(3, true),
                'ru' => $this->faker->words(3, true),
            ],
            'code' => $this->faker->unique()->regexify('[A-Z]{3}-[0-9]{6}'),
            'description' => [
                'ua' => $this->faker->paragraphs(2, true),
                'ru' => $this->faker->paragraphs(2, true),
            ],
            'short_description' => [
                'ua' => $this->faker->paragraph(),
                'ru' => $this->faker->paragraph(),
            ],
            'price' => $this->faker->numberBetween(500, 50000) / 100,
            'price_old' => $this->faker->numberBetween(600, 60000) / 100,
            'status' => 'active',
            'picture' => null,
            'other_pictures' => [],
            'link_to_youtube' => [],
            'is_active' => true,
            'category_id' => null,
            'slug' => $this->faker->unique()->slug(),
            'external_id' => null,
            'analogs' => [],
            'other_categories' => [],
            'priority' => 0,
        ];
    }
}
