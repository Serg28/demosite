<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Bezhanov\Faker\Provider\Commerce;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = \Faker\Factory::create('ru_RU');
        $faker->addProvider(new Commerce($faker));

        $title = $faker->productName;
        $shortDescription = $faker->sentence(10);

        $price = $faker->numberBetween(100, 1000);

        $category = Category::withDepth()->having('depth', '>', 2)->orderByRaw('RAND()')->first();

        return [
            'title' => json_encode([
                'ru' => $title,
                'ua' => $title,
                'en' => $title,
            ]),

            'short_description' => json_encode([
                'ru' => $shortDescription,
                'ua' => $shortDescription,
                'en' => $shortDescription,
            ]),

            'description' => json_encode([
                'ru' => $faker->sentence(20),
                'ua' => $faker->sentence(20),
                'en' => $faker->sentence(20),
            ]),

            'slug' => Str::slug($title),

            'picture' => $faker->randomElement(
                [
                    '/images/holodilnik-nauchili-raspoznavat-produkty.jpeg',
                    '/images/proverim-ka-chto-vy-tam-svarili.jpeg',
                    '/products/5abd2978f0222fdaf2a81f1a96e9fea53f002943.jpeg',
                    '/products/hype-ru-black-friday-2017-deals-l-1517896081-132_large.png',
                    '/products/Thinkbook_Plus_1.jpeg',
                    '/products/U0360294_big.jpeg',
                    '/products/uluchshit-zdorove-ekologicheski-chistaya-pishcha-1024x683.jpeg',
                    '/storage/editor/fotos/946bd7ed3d27fd993f324d83eaee0307_1623066551.jpg',
                    '/storage/editor/fotos/946bd7ed3d27fd993f324d83eaee0307_1623066551.jpg',
                    '/storage/editor/fotos/09b236b72926c083eab17034c5245c07_1623073266.jpg',
                    '/storage/editor/fotos/b960ab3df74638a67dbb83125cbd4a6b_1623068801.png',
                ]),
            'is_active' => 1,
            'article' => $faker->promotionCode,
            'price' => $price,
            'price_old' => $price + $faker->numberBetween(100, 200),
            'category_id' => $category->id,
            'product_status_id' => 1,
        ];
    }
}
