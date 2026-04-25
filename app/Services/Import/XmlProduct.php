<?php

namespace App\Services\Import;

use App\Models\Category;
use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Models\Product;
use App\Models\ProductCharacteristicOption;
use ErrorException;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelEx;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Rodenastyle\StreamParser\StreamParser;
use Tightenco\Collect\Support\Collection;

class XmlProduct
{
    private $url;

    public function __construct($url)
    {
        $this->url = $url;

        \DB::disableQueryLog();
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

    public function get(): void
    {
        $this->createAnotherCategory();
        $this->getCategories($this->url);
        $this->getProducts($this->url);
    }

    private function getCategories($url): void
    {
        StreamParser::xml($url)->each(function (Collection $shop): void {
            $categories = $shop->get('categories')->toArray();
            foreach ($categories as $category) {
                try {
                    $this->saveCategory($category);
                } catch (ModelEx $e) {
                }
            }
        });
    }

    private function getProducts($url): void
    {
        $xmlString = file_get_contents($url);
        $xmlObject = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);

        $characteristics = $this->getCharacteristics($xmlObject);

        $json = json_encode($xmlObject);
        $phpArray = json_decode($json, true);
        $products = $phpArray['shop']['offers']['offer'];

        foreach ($products as $key => $product) {
            $this->saveProduct($product, $characteristics['offer'][$key]);
        }
    }

    private function getCharacteristics($xml)
    {
        $characteristics = [];
        foreach ($xml->{'shop'}->{'offers'}->{'offer'} as $key => $product) {
            $arr = [];
            foreach ($product->{'param'} as $param) {
                $arr[] = [
                    'name' => (string) $param['name'],
                    'value' => (string) $param[0],
                ];
            }
            $characteristics[$key][] = $arr;
        }

        return $characteristics;
    }

    private function saveCategory($data): void
    {
        if ($data['id'] > 1) {
            $category = Category::find($data['id']);
            if (is_null($category)) {
                $category = new Category();
            }
            $this->updateOrInsertCategory($category, $data);
        }
    }

    private function saveProduct($data, $characteristics): void
    {
        if (array_key_exists('picture', $data)) {
            $pictures = $this->preparePicturesForSaving($data['picture']);
            $data['picture'] = $pictures['picture'];
            $data['other_pictures'] = $pictures['other_pictures'];
        }
        $data['slug'] = $data['@attributes']['id'].''.Str::slug($data['name']);
        $data['id'] = $data['@attributes']['id'];
        $data['title'] = json_encode(['ua' => $data['name'], 'ru' => '', 'en' => '']);

        if (is_null(Category::find($data['categoryId']))) {
            $data['categoryId'] = 9999;
        }

        if (array_key_exists('description', $data)) {
            $data['description'] = json_encode(['ua' => $data['description'], 'ru' => '', 'en' => '']);
        } else {
            $data['description'] = json_encode(['ua' => $data['name'], 'ru' => '', 'en' => '']);
        }
        $product = Product::find($data['id']);
        if (is_null($product)) {
            $product = new Product();
        }
        $product = $this->updateOrInsertProduct($product, $data);

        if (array_key_exists('vendor', $data)) {
            $this->addCharacteristic(['brend', $data['vendor']], $product);
        }
        if (isset($characteristics)) {
            foreach ($characteristics as $characteristic) {
                $this->addCharacteristic([$characteristic['name'], $characteristic['value']], $product);
            }
        }
    }

    private function addCharacteristic($characteristicStr, $product): void
    {
        $slugCharacteristic = isset($characteristicStr[0]) ? Str::slug($characteristicStr[0]) : null;
        $slugOption = isset($characteristicStr[1]) ? Str::slug($characteristicStr[1]) : null;

        $characteristic = Characteristic::where('slug', $slugCharacteristic)->first();
        $option = CharacteristicOption::where('slug', $slugOption)->first();

        if (! $characteristic) {
            $characteristic = $this->updateOrInsertCharacteristic($characteristicStr[0], $slugCharacteristic);
        }
        if (! $option && isset($characteristicStr[1])) {
            $option = $this->updateOrInsertCharacteristicOption($characteristicStr[1], $slugOption);
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

        if (isset($characteristicStr[1]) && ! $product->characteristics()->where('product_characteristic_options.characteristic_option_id', $option->id)->exists()) {
            $product->characteristics()->save($this->updateOrInsertProductCharacteristicOption($characteristic->id, $option->id));
        }
    }

    private function updateOrInsertCategory($instance, $data): void
    {
        $instance->id = $data['id'];
        $instance->title = json_encode(['ua' => $data['category'], 'ru' => '', 'en' => '']);
        $instance->parent_id = array_key_exists('parentId', $data) ? $data['parentId'] : 1;
        $instance->slug = Str::slug($data['category']) ?? null;
        $instance->is_active = 1;

        $instance->save();
    }

    private function updateOrInsertProduct($instance, $data)
    {
        $instance->id = $data['id'];
        $instance->category_id = $data['categoryId'] ?? 1;
        $instance->title = $data['title'];
        $instance->description = $data['description'];
        $instance->price = $data['price'];
        $instance->price_old = $data['oldprice'] ?? '';
        $instance->slug = $data['slug'];
        $instance->is_active = 1;
        $instance->product_status_id = $data['quantity_in_stock'] ? 1 : 5;
        $instance->quantity = $data['quantity_in_stock'] ?? 0;
        $instance->picture = $data['picture'] ?? '';
        $instance->other_pictures = $data['other_pictures'] ?? '';
        $instance->code = $data['vendorCode'] ?? 0;
        $instance->save();

        return $instance;
    }

    private function updateOrInsertCharacteristic($title, $slug)
    {
        return Characteristic::create([
            'title' => json_encode(['ua' => $title, 'ru' => '', 'en' => '']),
            'slug' => $slug,
            'is_active' => 1,
        ]);
    }

    private function updateOrInsertCharacteristicOption($title, $slug)
    {
        return new CharacteristicOption([
            'title' => json_encode(['ua' => $title, 'ru' => '', 'en' => '']),
            'slug' => $slug,
            'is_active' => 1,
        ]);
    }

    private function updateOrInsertProductCharacteristicOption($characteristicId, $optionId)
    {
        return new ProductCharacteristicOption([
            'characteristic_id' => $characteristicId,
            'characteristic_option_id' => $optionId,
        ]);
    }

    private function preparePicturesForSaving($data)
    {
        $picture = '';
        $other_pictures = [];

        if (is_array($data)) {
            $picture = $this->storePicture($data[0]);
            for ($index = 1; $index < count($data); $index++) {
                $other_pictures[] = $this->storePicture($data[$index]);
            }
            $other_pictures = json_encode($other_pictures, JSON_UNESCAPED_SLASHES);
        } else {
            $picture = $this->storePicture($data);
        }

        return ['picture' => $picture,
            'other_pictures' => empty($other_pictures) ? '' : $other_pictures,
        ];
    }

    private function storePicture($urlPicture)
    {
        try {
            $content = file_get_contents($urlPicture);
            if (! file_exists('storage/editor/fotos/'.pathinfo($urlPicture)['basename']) && $content) {
                Storage::disk('editor')->put('fotos/'.pathinfo($urlPicture)['basename'], $content);

                return '/storage/editor/fotos/'.pathinfo($urlPicture)['basename'];
            }
        } catch (ErrorException $e) {
        }
    }

    private function createAnotherCategory(): void
    {
        $data = [
            'id' => '9999',
            'parentId' => 1,
            'category' => 'Інше',
        ];
        $this->saveCategory($data);
    }
}
