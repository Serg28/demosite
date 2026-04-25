<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users
        User::factory(5)->create();
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create categories
        $categories = \App\Models\Category::factory(8)->create();

        // Create brands
        \App\Models\Brand::factory(12)->create();

        // Create products with random categories
        \App\Models\Product::factory(50)->create([
            'category_id' => fn () => $categories->random()->id,
        ]);

        // Create orders with products
        \App\Models\Order::factory(15)->create()->each(function ($order) {
            \App\Models\OrderProduct::factory(3)->create([
                'order_id' => $order->id,
            ]);
        });

        // Create comments for products
        \App\Models\Comment::factory(100)->create();
    }
}
