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
        $brands = \App\Models\Brand::factory(12)->create();

        // Create characteristics
        $characteristics = \App\Models\Characteristic::factory(8)->create();

        // Create characteristic options
        $characteristics->each(function ($characteristic) {
            \App\Models\CharacteristicOption::factory(5)->create([
                'characteristic_id' => $characteristic->id,
            ]);
        });

        // Associate characteristics with categories
        $categories->each(function ($category) use ($characteristics) {
            $category->characteristics()->attach($characteristics->random(3)->pluck('id'));
        });

        // Create products with random categories and brands
        $products = \App\Models\Product::factory(100)->create([
            'category_id' => fn () => $categories->random()->id,
            'brand_id' => fn () => $brands->random()->id,
        ]);

        // Associate products with characteristic options
        $products->each(function ($product) use ($characteristics) {
            $selectedChars = $characteristics->random(3);
            foreach ($selectedChars as $char) {
                $option = $char->options()->inRandomOrder()->first();
                if ($option) {
                    $product->characteristics()->attach($option->id);
                }
            }
        });

        // Create orders with products
        \App\Models\Order::factory(15)->create()->each(function ($order) use ($products) {
            \App\Models\OrderProduct::factory(3)->create([
                'order_id' => $order->id,
                'product_id' => fn () => $products->random()->id,
            ]);
        });

        // Create comments for products
        \App\Models\Comment::factory(100)->create([
            'product_id' => fn () => $products->random()->id,
        ]);
    }
}
