<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Models\Product;
use App\Models\ProductCharacteristicOption;
use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Rodenastyle\StreamParser\StreamParser;
use Tightenco\Collect\Support\Collection;

class XmlParserCommand extends Command
{
    protected $signature = 'xml:parser {file}';

    protected $description = 'xml parser';

    protected $urlExportLink;

    public function handle(): void
    {
        $this->urlExportLink = public_path($this->argument('file'));
        $this->createAnotherCategory();
        $this->getCategories();
        $this->getProducts();

        Artisan::call('cache:clear');
        Artisan::call('search:reindex');
    }

    private function createAnotherCategory(): void
    {
        $data = [
            'id' => '9999',
            'parentId' => 1,
            'category' => 'Інше',
        ];
        $this->updateOrInsertCategory($data);
    }

    private function getCategories(): void
    {
        StreamParser::xml($this->urlExportLink)->each(function (Collection $shop): void {
            $categories = $shop->get('categories')->toArray();

            foreach ($categories as $category) {
                try {
                    $this->updateOrInsertCategory($category);
                } catch (ModelNotFoundException $e) {
//                  until we catch the exception
                }
            }
        });
    }

    private function getProducts(): void
    {
        $xmlString = file_get_contents($this->urlExportLink);
        $xmlObject = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);

        $characteristics = $this->getCharacteristics($xmlObject);

        $phpArray = json_decode(json_encode($xmlObject), true);
        $products = $phpArray['shop']['offers']['offer'];

        foreach ($products as $key => $product) {
            $this->saveProduct($product, $characteristics['offer'][$key]);
        }
    }

    private function saveProduct($data, $characteristics): void
    {
        $product = $this->updateOrInsertProduct($data);

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

    private function updateOrInsertCharacteristic($title, $slug)
    {
        return Characteristic::create([
            'title' => $this->getStringJson($title),
            'slug' => $slug,
            'is_active' => 1,
        ]);
    }

    private function updateOrInsertCharacteristicOption($title, $slug)
    {
        return new CharacteristicOption([
            'title' => $this->getStringJson($title),
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

    private function updateOrInsertProduct($data)
    {
        $idProductForeign = $data['@attributes']['id'];

        $product = Product::firstWhere('code', $idProductForeign);

        Log::info('product');
        Log::info($data);

        if ($product) {
            $product->update([
                'quantity' => $data['stock_quantity'] ?? 0,
                'price' => $data['price'],
                'price_old' => $data['oldprice'] ?? '',
            ]);
            $this->info('update product-'.$data['name']);
        } else {
            $product = Product::create(
                [
                    'category_id' => $data['categoryId'] ?? 1,
                    'title' => $this->getStringJson($data['name']),
                    'description' => $this->getStringJson($data['description'] ?? ''),
                    'price' => $data['price'],
                    'price_old' => $data['oldprice'] ?? '',
                    'slug' => Str::slug($data['name']).''.$idProductForeign,
                    'is_active' => 1,
                    'product_status_id' => array_key_exists('stock_quantity', $data) ? 1 : 3,
                    'quantity' => array_key_exists('stock_quantity', $data) ? $data['stock_quantity'] : 0,
                    'picture' => $this->getMainPicture($data),
                    'other_pictures' => $this->getOtherPicture($data),
                    'code' => $idProductForeign,
                ]
            );

            $this->info('insert product-'.$data['name']);
        }

        return $product;
    }

    protected function getMainPicture($data)
    {
        if (isset($data['picture'])) {
            if (is_array($data['picture'])) {
                return $this->storePicture(array_shift($data['picture']));
            }

            return $this->storePicture($data['picture']);
        }

        return '';
    }

    protected function getOtherPicture($data)
    {
        if (isset($data['picture']) && is_array($data['picture'])) {
            array_shift($data['picture']);

            $result = [];
            if (is_array($data['picture'])) {
                foreach ($data['picture'] as $picture) {
                    $result[] = $this->storePicture($picture);
                }
            }

            return json_encode($result, JSON_UNESCAPED_SLASHES);
        }

        return json_encode([]);
    }

    private function storePicture($urlPicture)
    {
        try {
            if (! file_exists('storage/editor/fotos/'.pathinfo($urlPicture)['basename'])) {
                $content = file_get_contents($urlPicture);

                Storage::disk('editor')->put('fotos/'.pathinfo($urlPicture)['basename'], $content);

                return '/storage/editor/fotos/'.pathinfo($urlPicture)['basename'];
            }
        } catch (ErrorException $e) {
            $this->error($e->getMessage());
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

    private function updateOrInsertCategory($data): void
    {
        Category::updateOrCreate(
            ['id' => $data['id']],
            [
                'title' => $this->getStringJson($data['category']),
                'parent_id' => array_key_exists('parentId', $data) ? $data['parentId'] : 1,
                'slug' => Str::slug($data['category']),
                'is_active' => 1,
            ]
        );

        $this->info('update/insert category - '.$data['category']);
    }

    private function getStringJson($title)
    {
        return json_encode(['ru' => '', 'ua' => $title, 'en' => '']);
    }
}
