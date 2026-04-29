<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Realistic demo data for catalog:
 *  - 8 categories (electronics)
 *  - 10 characteristics, several with 15-20 options (triggers "Show more in group" + search)
 *  - 220 products distributed across categories with characteristic options assigned
 *
 * Run: php artisan db:seed --class=CatalogDemoSeeder
 * Or via migrate:fresh --seed (called from DatabaseSeeder)
 */
class CatalogDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->truncate();

        $chars  = $this->createCharacteristics();
        $cats   = $this->createCategories($chars);
        $this->createProducts($cats, $chars);
    }

    // ─── Schema ──────────────────────────────────────────────────────────────

    private function characteristicSchema(): array
    {
        return [
            'brand' => [
                'title'        => ['ua' => 'Бренд',              'ru' => 'Бренд',              'en' => 'Brand'],
                'is_range_type' => false,
                'priority'     => 100,
                'options'      => [
                    'Apple', 'Samsung', 'Xiaomi', 'Lenovo', 'HP',
                    'Dell', 'ASUS', 'Acer', 'LG', 'Sony',
                    'Huawei', 'OnePlus', 'Realme', 'Motorola', 'Google',
                    'MSI', 'Razer', 'Toshiba', 'Panasonic', 'Philips',
                ],
            ],
            'color' => [
                'title'        => ['ua' => 'Колір',              'ru' => 'Цвет',               'en' => 'Color'],
                'is_range_type' => false,
                'priority'     => 90,
                'options'      => [
                    'Чорний', 'Білий', 'Сірий', 'Срібний', 'Золотий',
                    'Синій', 'Темно-синій', 'Зелений', 'Червоний', 'Рожевий',
                    'Жовтий', 'Помаранчевий', 'Фіолетовий', 'Бежевий',
                    'Коричневий', 'Титановий', 'Рожеве золото', 'Небесно-блакитний',
                ],
            ],
            'cpu' => [
                'title'        => ['ua' => 'Процесор',           'ru' => 'Процессор',          'en' => 'Processor'],
                'is_range_type' => false,
                'priority'     => 80,
                'options'      => [
                    'Intel Core i3-1215U', 'Intel Core i5-1235U', 'Intel Core i7-1255U',
                    'Intel Core i9-12900H', 'Intel Core Ultra 5', 'Intel Core Ultra 7',
                    'AMD Ryzen 5 5500U', 'AMD Ryzen 5 6600H', 'AMD Ryzen 7 5700U',
                    'AMD Ryzen 7 6800H', 'AMD Ryzen 9 6900HX', 'AMD Ryzen 9 7945HX',
                    'Apple M1', 'Apple M2', 'Apple M3 Pro',
                ],
            ],
            'ram' => [
                'title'        => ['ua' => "Об'єм ОЗП",         'ru' => 'Объём ОЗУ',          'en' => 'RAM'],
                'is_range_type' => false,
                'priority'     => 70,
                'options'      => ['4 GB', '6 GB', '8 GB', '12 GB', '16 GB', '24 GB', '32 GB', '64 GB'],
            ],
            'storage' => [
                'title'        => ['ua' => 'Накопичувач',        'ru' => 'Накопитель',         'en' => 'Storage'],
                'is_range_type' => false,
                'priority'     => 65,
                'options'      => [
                    '64 GB', '128 GB', '256 GB', '512 GB',
                    '1 TB SSD', '2 TB SSD', '4 TB SSD',
                    '256 GB HDD', '512 GB HDD', '1 TB HDD', '2 TB HDD',
                ],
            ],
            'resolution' => [
                'title'        => ['ua' => 'Роздільна здатність', 'ru' => 'Разрешение',        'en' => 'Resolution'],
                'is_range_type' => false,
                'priority'     => 60,
                'options'      => [
                    '1280×720 HD', '1920×1080 Full HD', '2560×1080 Ultra Wide',
                    '2560×1440 QHD', '3440×1440 Ultra Wide QHD',
                    '3840×2160 4K UHD', '7680×4320 8K UHD',
                    '1024×768 XGA', '1280×800 WXGA', '1600×900 HD+',
                    '4096×2160 4K DCI', '6000×4000 RAW',
                ],
            ],
            'diagonal' => [
                'title'        => ['ua' => 'Діагональ, дюйм',   'ru' => 'Диагональ, дюйм',   'en' => 'Screen size, inch'],
                'is_range_type' => true,
                'priority'     => 55,
                'options'      => [
                    '11"', '12"', '13"', '13.3"', '14"',
                    '14.6"', '15.6"', '16"', '17"', '17.3"',
                    '24"', '27"', '32"', '43"', '50"',
                    '55"', '65"', '75"', '85"',
                ],
            ],
            'guarantee' => [
                'title'        => ['ua' => 'Гарантія',           'ru' => 'Гарантия',           'en' => 'Warranty'],
                'is_range_type' => false,
                'priority'     => 50,
                'options'      => [
                    '3 місяці', '6 місяців', '1 рік', '14 місяців',
                    '18 місяців', '2 роки', '24 місяці',
                    '3 роки', '36 місяців', '5 років',
                ],
            ],
            'material' => [
                'title'        => ['ua' => 'Матеріал корпусу',  'ru' => 'Материал корпуса',   'en' => 'Body material'],
                'is_range_type' => false,
                'priority'     => 40,
                'options'      => [
                    'Алюміній', 'Магнієвий сплав', 'Карбон', 'Пластик ABS',
                    'Полікарбонат', 'Скло Corning Gorilla', 'Нержавіюча сталь',
                    'Титановий сплав', 'Силікон', 'Бамбук',
                    'Перероблений пластик', 'Кевлар',
                ],
            ],
            'connection' => [
                'title'        => ['ua' => 'Тип підключення',   'ru' => 'Тип подключения',    'en' => 'Connection type'],
                'is_range_type' => false,
                'priority'     => 30,
                'options'      => [
                    'Bluetooth 5.0', 'Bluetooth 5.1', 'Bluetooth 5.2', 'Bluetooth 5.3',
                    'USB-C', 'USB-A', '3.5 мм Jack', 'Lightning',
                    'Wi-Fi 6', 'NFC', '2.4 ГГц бездротове', 'TWS',
                ],
            ],
        ];
    }

    private function categorySchema(): array
    {
        return [
            'laptops' => [
                'title'   => ['ua' => 'Ноутбуки',    'ru' => 'Ноутбуки',    'en' => 'Laptops'],
                'chars'   => ['brand', 'color', 'cpu', 'ram', 'storage', 'diagonal', 'guarantee', 'material'],
                'count'   => 80,
                'name_ua' => 'Ноутбук',
            ],
            'smartphones' => [
                'title'   => ['ua' => 'Смартфони',   'ru' => 'Смартфоны',   'en' => 'Smartphones'],
                'chars'   => ['brand', 'color', 'ram', 'storage', 'guarantee', 'material'],
                'count'   => 30,
                'name_ua' => 'Смартфон',
            ],
            'tvs' => [
                'title'   => ['ua' => 'Телевізори',  'ru' => 'Телевизоры',  'en' => 'TVs'],
                'chars'   => ['brand', 'resolution', 'diagonal', 'guarantee'],
                'count'   => 20,
                'name_ua' => 'Телевізор',
            ],
            'headphones' => [
                'title'   => ['ua' => 'Навушники',   'ru' => 'Наушники',    'en' => 'Headphones'],
                'chars'   => ['brand', 'color', 'connection', 'guarantee'],
                'count'   => 25,
                'name_ua' => 'Навушники',
            ],
            'tablets' => [
                'title'   => ['ua' => 'Планшети',    'ru' => 'Планшеты',    'en' => 'Tablets'],
                'chars'   => ['brand', 'color', 'cpu', 'ram', 'storage', 'guarantee'],
                'count'   => 20,
                'name_ua' => 'Планшет',
            ],
            'cameras' => [
                'title'   => ['ua' => 'Фотоапарати', 'ru' => 'Фотоаппараты', 'en' => 'Cameras'],
                'chars'   => ['brand', 'resolution', 'guarantee'],
                'count'   => 15,
                'name_ua' => 'Фотоапарат',
            ],
            'printers' => [
                'title'   => ['ua' => 'Принтери',    'ru' => 'Принтеры',    'en' => 'Printers'],
                'chars'   => ['brand', 'guarantee'],
                'count'   => 10,
                'name_ua' => 'Принтер',
            ],
            'monitors' => [
                'title'   => ['ua' => 'Монітори',    'ru' => 'Мониторы',    'en' => 'Monitors'],
                'chars'   => ['brand', 'resolution', 'diagonal', 'guarantee'],
                'count'   => 20,
                'name_ua' => 'Монітор',
            ],
        ];
    }

    // ─── Build ───────────────────────────────────────────────────────────────

    /** @return array<string, Characteristic> keyed by slug */
    private function createCharacteristics(): array
    {
        $result = [];

        foreach ($this->characteristicSchema() as $slug => $data) {
            $char = Characteristic::create([
                'title'        => $data['title'],
                'slug'         => $slug,
                'is_range_type' => $data['is_range_type'],
                'is_active'    => true,
                'priority'     => $data['priority'],
            ]);

            foreach ($data['options'] as $i => $optTitle) {
                CharacteristicOption::create([
                    'characteristic_id' => $char->id,
                    'title'             => ['ua' => $optTitle, 'ru' => $optTitle, 'en' => $optTitle],
                    'slug'              => Str::slug($slug . '-' . $optTitle),
                    'value'             => (string) ($i + 1),
                    'priority'          => count($data['options']) - $i,
                ]);
            }

            $char->load('options');
            $result[$slug] = $char;
        }

        return $result;
    }

    /**
     * @param  array<string, Characteristic>  $chars
     * @return array<string, array{model: Category, chars: string[], name_ua: string}>
     */
    private function createCategories(array $chars): array
    {
        $result = [];
        $priority = 100;
        $lft = 1;

        foreach ($this->categorySchema() as $slug => $data) {
            $cat = Category::create([
                'title'     => $data['title'],
                'slug'      => $slug,
                'is_active' => true,
                'priority'  => $priority,
                'lft'       => $lft,
                'rgt'       => $lft + 1,
                'depth'     => 0,
            ]);
            $lft += 2;

            $charIds = collect($data['chars'])
                ->map(fn ($k) => $chars[$k]->id)
                ->all();

            $cat->characteristics()->attach($charIds);

            $result[$slug] = [
                'model'   => $cat,
                'chars'   => $data['chars'],
                'count'   => $data['count'],
                'name_ua' => $data['name_ua'],
            ];

            $priority -= 10;
        }

        return $result;
    }

    /**
     * @param  array<string, array{model: Category, chars: string[], count: int, name_ua: string}>  $cats
     * @param  array<string, Characteristic>  $chars
     */
    private function createProducts(array $cats, array $chars): void
    {
        $counter = 1;

        foreach ($cats as $catSlug => $catData) {
            $category = $catData['model'];
            $catChars = $catData['chars'];
            $nameUa   = $catData['name_ua'];

            // Pre-load options for this category's characteristics
            $optionsByChar = [];
            foreach ($catChars as $charSlug) {
                $optionsByChar[$charSlug] = $chars[$charSlug]->options->all();
            }

            for ($i = 0; $i < $catData['count']; $i++) {
                // Pick a brand for the product name
                $brandOptions = $optionsByChar['brand'] ?? [];
                $brandName = $brandOptions
                    ? ($brandOptions[array_rand($brandOptions)]->title['ua'] ?? '')
                    : '';

                $index = str_pad((string) $counter, 3, '0', STR_PAD_LEFT);
                $title = trim("{$nameUa} {$brandName} {$index}");

                $price    = random_int(499, 89999);
                $priceOld = random_int(1, 10) > 7 ? (int) ($price * 1.15) : null;

                $product = Product::create([
                    'title'       => ['ua' => $title, 'ru' => $title, 'en' => $title],
                    'slug'        => Str::slug($title . '-' . $counter),
                    'code'        => 'DEMO-' . strtoupper($catSlug) . '-' . $index,
                    'description' => [
                        'ua' => "Опис: {$title}",
                        'ru' => "Описание: {$title}",
                        'en' => "Description: {$title}",
                    ],
                    'price'       => $price,
                    'price_old'   => $priceOld,
                    'is_active'   => true,
                    'category_id' => $category->id,
                    'priority'    => random_int(1, 100),
                ]);

                // Assign one random option per characteristic
                foreach ($catChars as $charSlug) {
                    $options = $optionsByChar[$charSlug] ?? [];
                    if (empty($options)) {
                        continue;
                    }
                    $option = $options[array_rand($options)];
                    DB::table('product_characteristic_options')->insert([
                        'product_id'               => $product->id,
                        'characteristic_option_id'  => $option->id,
                        'created_at'               => now(),
                        'updated_at'               => now(),
                    ]);
                }

                $counter++;
            }
        }
    }

    // ─── Cleanup ─────────────────────────────────────────────────────────────

    private function truncate(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('product_characteristic_options')->truncate();
        DB::table('characteristic_options')->truncate();
        DB::table('category_characteristic')->truncate();
        DB::table('characteristics')->truncate();
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
