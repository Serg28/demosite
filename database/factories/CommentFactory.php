<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'user_id' => null,
            'rating' => $this->faker->numberBetween(1, 5),
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'text' => $this->faker->paragraph(),
            'is_active' => false,
            'external_id' => null,
        ];
    }
}
