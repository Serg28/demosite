<?php

namespace App\Services\Import;

use App\Models\Category;
use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Models\Product;
use App\Models\ProductCharacteristicOption;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Rodenastyle\StreamParser\StreamParser;
use Tightenco\Collect\Support\Collection;

class CsCard
{
    private $url;

    public function __construct($url)
    {
        $this->url = $url;

        $this->cleanTable('categories');
        $this->cleanTable('category_characteristic');
        $this->cleanTable('products');
        $this->cleanTable('product_characteristic_options');
        $this->cleanTable('characteristics');
        $this->cleanTable('characteristic_options');

        \DB::disableQueryLog();
    }

    public function get(): void
    {
        StreamParser::xml($this->url)->each(function (Collection $shop): void {
            $categoriesArr = $shop->get('categories')->toArray();

            foreach ($categoriesArr as $category) {
                $this->insertCategory($category);
            }

            $productsArr = $shop->get('offers')->toArray();

            foreach ($productsArr as $product) {
                $this->insertProduct($product);
            }
        });
    }

    private function cleanTable($nameTb): void
    {
        if ($nameTb == 'categories') {
            DB::table($nameTb)
                ->where('id', '>', 1)
                ->delete();
        } else {
            DB::table($nameTb)->delete();
        }
    }

    private function insertCategory($data): void
    {
        Category::create([
            'id' => $data['id'],
            'title' => json_encode(['ua' => $data['category'], 'ru' => '', 'en' => '']),
            'parent_id' => $data['parentId'] ?? 1,
            'slug' => Str::slug($data['category']) ?? null,
            'is_active' => 1,
        ]);
    }

    private function insertProduct($data): void
    {
        $pathToPicture = $this->storePicture($data['picture']);

        $slug = trim(parse_url($data['url'], PHP_URL_PATH), '/');
        $slug = str_replace('.html', '', $slug);

        $product = Product::create([
            'id' => $data['id'],
            'category_id' => $data['categoryId'],
            'title' => json_encode(['ua' => $data['name'], 'ru' => '', 'en' => '']),
            'description' => json_encode(['ua' => $data['description'], 'ru' => '', 'en' => '']) ?? json_encode(['ua' => $data['name'], 'ru' => '', 'en' => '']),
            'price' => $data['price'] ?? null,
            'price_old' => $data['price_old'] ?? 0,
            'slug' => $slug ?? null,
            'is_active' => 1,
            'product_status_id' => 1,
            'picture' => $pathToPicture,
        ]);

        if (array_key_exists('param', $data)) {
            $params = explode('<br />', $data['param']['param']);

            foreach ($params as $param) {
                $this->addCharacteristic($param, $product);
            }
        }
    }

    private function addCharacteristic($characteristicStr, $product): void
    {
        $paramsExp = explode(':', $characteristicStr);

        $slugCharacteristic = Str::slug($paramsExp[0]);
        $slugOption = Str::slug($paramsExp[1]);

        $characteristic = Characteristic::where('slug', $slugCharacteristic)->first();
        $option = CharacteristicOption::where('slug', $slugOption)->first();

        if (! $characteristic) {
            $characteristic = $this->insertCharacteristic($paramsExp[0], $slugCharacteristic);
        }
        if (! $option) {
            $option = $this->insertCharacteristicOption($paramsExp[1], $slugOption);
            $characteristic->options()->save($option);
        }
        $category = $product->category;
        $add = true;
        foreach ($category->characteristics as $char) {
            if ($char->id == $characteristic->id) {
                $add = false;
                break;
            }
        }
        if ($add) {
            $category->characteristics()->attach($characteristic);
        }

        $product->characteristics()->save($this->insertProductCharacteristicOption($characteristic->id, $option->id));
    }

    private function insertCharacteristic($title, $slug)
    {
        return Characteristic::create([
            'title' => json_encode(['ua' => $title, 'ru' => '', 'en' => '']),
            'slug' => $slug,
            'is_active' => 1,
        ]);
    }

    private function insertCharacteristicOption($title, $slug)
    {
        return new CharacteristicOption([
            'title' => json_encode(['ua' => $title, 'ru' => '', 'en' => '']),
            'slug' => $slug,
            'is_active' => 1,
        ]);
    }

    private function insertProductCharacteristicOption($characteristicId, $optionId)
    {
        return new ProductCharacteristicOption([
            'characteristic_id' => $characteristicId,
            'characteristic_option_id' => $optionId,
        ]);
    }

    private function storePicture($urlPicture)
    {
        try {
            $content = file_get_contents($urlPicture);
            if (! file_exists('storage/editor/fotos/'.pathinfo($urlPicture)['basename']) && $content) {
                Storage::disk('editor')->put('fotos/'.pathinfo($urlPicture)['basename'], $content);

                return '/storage/editor/fotos/'.pathinfo($urlPicture)['basename'];
            }
        } catch (Exception $e) {
//            until we catch the exception
        }
    }
}
