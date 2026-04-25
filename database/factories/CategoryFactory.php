<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'lft' => 1,
            'rgt' => 2,
            'depth' => 0,
            'title' => [
                'ua' => $this->faker->words(2, true),
                'ru' => $this->faker->words(2, true),
            ],
            'slug' => $this->faker->unique()->slug(),
            'picture' => null,
            'is_active' => true,
        ];
    }
}
