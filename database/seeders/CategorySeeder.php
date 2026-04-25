<?php

namespace Database\Seeders;

use App\Models\Category;
use Bezhanov\Faker\Provider\Commerce;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('ru_RU');
        $faker->addProvider(new Commerce($faker));

        // Create 5 records
        for ($i = 0; $i < 5; $i++) {
            $title = $faker->productName;
            Category::create([

                'title' => json_encode([
                    'ru' => $title,
                    'ua' => $title,
                    'en' => $title,
                ]),
                'parent_id' => 1,
                'slug' => Str::slug($title.time()),
                'is_active' => 1,
                'picture' => $faker->imageUrl(640, 480),
            ]);
        }

        Category::fixTree();
    }
}
