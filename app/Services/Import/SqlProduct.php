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

class SqlProduct
{
    public function __construct()
    {
        $this->cleanTable('category_characteristic');

        \DB::disableQueryLog();
    }

    public function parse(): void
    {
        $this->selectCategoryCharacteristics();
        $this->selectProductPictures();
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

    private function selectCategories(): void
    {
        $selectCategories = "SELECT cscart_categories.category_id AS id,
                                    cscart_categories.parent_id,
                                    cscart_category_descriptions.category AS title,
                                    cscart_category_descriptions.lang_code AS lang,
                                    cscart_seo_names.name AS slug
                            FROM cscart_categories
                                JOIN cscart_category_descriptions
                                    ON  cscart_categories.category_id=cscart_category_descriptions.category_id AND cscart_categories.status = 'A'
                                JOIN cscart_seo_names
                                    ON cscart_categories.category_id=cscart_seo_names.object_id AND cscart_seo_names.type='c'
                                ORDER BY id";

        $categories = DB::connection('mysql2')->select($selectCategories);

        $this->checkAndSave($categories, 'categories');
    }

    private function selectCharacteristics(): void
    {
        $selectCharacteristics = 'SELECT cscart_product_features_descriptions.feature_id AS id,
                                         cscart_product_features_descriptions.description AS title,
                                         cscart_product_features_descriptions.lang_code AS lang
                                  FROM cscart_product_features_descriptions
                                    ORDER BY id';

        $characteristics = DB::connection('mysql2')->select($selectCharacteristics);

        $this->checkAndSave($characteristics, 'characteristics');
    }

    private function selectCharacteristicOptions(): void
    {
        $selectOptions = 'SELECT cscart_product_feature_variant_descriptions.variant_id AS id,
                                 cscart_product_feature_variant_descriptions.variant AS title,
                                 cscart_product_feature_variant_descriptions.lang_code AS lang,
                                 cscart_product_feature_variants.feature_id as characteristic_id
                          FROM cscart_product_feature_variant_descriptions
                            JOIN cscart_product_feature_variants
                                ON cscart_product_feature_variant_descriptions.variant_id=cscart_product_feature_variants.variant_id
                            ORDER BY id';

        $options = DB::connection('mysql2')->select($selectOptions);

        $this->checkAndSave($options, 'characteristic_options');
    }

    private function selectProducts(): void
    {
        $selectProducts = "SELECT cscart_products.product_id AS id,
                                  cscart_products.product_code AS code,
                                  cscart_products.amount AS quantity,
                                  cscart_products.list_price AS price_old,
                                  cscart_product_prices.price AS price,
                                  cscart_product_descriptions.product AS title,
                                  cscart_product_descriptions.lang_code AS lang,
                                  cscart_product_descriptions.short_description,
                                  cscart_product_descriptions.full_description AS description,
                                  cscart_products_categories.category_id,
                                  cscart_seo_names.name AS slug
                          FROM cscart_products
                            JOIN  cscart_product_descriptions
                                ON cscart_products.product_id=cscart_product_descriptions.product_id AND cscart_products.status = 'A'
                            JOIN cscart_products_categories
                                ON cscart_products.product_id=cscart_products_categories.product_id
                            JOIN  cscart_product_prices
                                ON cscart_products.product_id=cscart_product_prices.product_id
                            JOIN cscart_seo_names
                                ON cscart_products.product_id=cscart_seo_names.object_id AND cscart_seo_names.type='p'
                            ORDER BY id";

        $products = DB::connection('mysql2')->select($selectProducts);

        $name = [];
        $short_description = [];
        $description = [];

        for ($i = 0; $i < count($products); $i++) {
            if (array_key_exists($i + 1, $products) && $products[$i]->id == $products[$i + 1]->id) {
                $name[$products[$i]->lang] = $products[$i]->title;
                $short_description[$products[$i]->lang] = $products[$i]->short_description;
                $description[$products[$i]->lang] = $products[$i]->description;
            } else {
                $name[$products[$i]->lang] = $products[$i]->title;
                $short_description[$products[$i]->lang] = $products[$i]->short_description;
                $description[$products[$i]->lang] = $products[$i]->description;

                $products[$i]->title = $this->formJson($name);
                $products[$i]->short_description = $this->formJson($short_description);
                $products[$i]->description = $this->formJson($description);

                try {
                    $this->insertProduct($products[$i]);
                    throw new Exception('Something bad');
                } catch (Exception $e) {
                }
                $name = [];
                $short_description = [];
                $description = [];
            }
        }
    }

    private function selectProductCharacteristicOptions(): void
    {
        $selectProductCharacteristicOption = "SELECT cscart_product_features_values.feature_id AS characteristic_id,
                                                     cscart_product_features_values.product_id,
                                                     cscart_product_features_values.variant_id AS characteristic_option_id,
                                                     cscart_product_features_values.lang_code AS lang,
                                                     cscart_product_features_values.value AS value
                                             FROM cscart_product_features_values
                                                WHERE cscart_product_features_values.lang_code='uk'
                                             ORDER BY product_id";

        $productCharacteristicOptions = DB::connection('mysql2')->select($selectProductCharacteristicOption);

        for ($i = 0; $i < count($productCharacteristicOptions); $i++) {
            if (empty($productCharacteristicOptions[$i]->value)) {
                try {
                    $this->insertProductCharacteristicOption($productCharacteristicOptions[$i]);
                    throw new Exception('Something bad');
                } catch (Exception $e) {
                }
            }
        }
    }

    private function selectProductPictures(): void
    {
        $selectProductPictures = "SELECT cscart_images_links.object_id AS id,
                                         cscart_images_links.type,
                                         cscart_images.image_path AS picture,
                                         cscart_images.image_id
                                 FROM cscart_images_links
                                    JOIN  cscart_images
                                        ON cscart_images_links.detailed_id=cscart_images.image_id AND cscart_images_links.object_type='product'
                                    ORDER BY id";
        $productPictures = DB::connection('mysql2')->select($selectProductPictures);

        $other_pictures = [];
        $picture = '';
        for ($i = 0; $i < count($productPictures); $i++) {
            $detailedId = substr($productPictures[$i]->image_id, 0, 2);
            $pathToPicture = 'https://stremyanka.com.ua/images/detailed/'.$detailedId.'/'.$productPictures[$i]->picture;

            switch ($productPictures[$i]->type) {
                case 'A':
                    $other_pictures[] = $this->storePicture($pathToPicture);
                    break;
                case 'M':
                    $picture = $this->storePicture($pathToPicture);
                    break;
            }
            if (array_key_exists($i + 1, $productPictures) && $productPictures[$i]->id != $productPictures[$i + 1]->id) {
                $productPictures[$i]->picture = $picture;
                $productPictures[$i]->other_pictures = ! empty($other_pictures) ? json_encode($other_pictures, JSON_UNESCAPED_SLASHES) : '';
                if (! empty($productPictures[$i]->picture) || ! empty($productPictures[$i]->other_pictures)) {
                    $this->insertProductPictures($productPictures[$i]);
                }

                $other_pictures = [];
                $picture = '';
            }
        }
    }

    private function selectCategoryCharacteristics(): void
    {
        $select = 'SELECT products.category_id,
                          product_characteristic_options.characteristic_id
                   FROM products
                       JOIN product_characteristic_options
                            ON products.id=product_characteristic_options.product_id
                            ORDER BY category_id';
        $categoryCharacteristics = DB::connection('mysql')->select($select);

        foreach ($categoryCharacteristics as $categoryCharacteristic) {
            $category = Category::find($categoryCharacteristic->category_id);
            $characteristic = Characteristic::find($categoryCharacteristic->characteristic_id);

            if (! $category->characteristics()->where('characteristics.id', $characteristic->id)->exists()) {
                $category->characteristics()->attach($characteristic);
            }
        }
    }

    private function checkAndSave($data, $table): void
    {
        $name = [];
        for ($i = 0; $i < count($data); $i++) {
            if (array_key_exists($i + 1, $data) && $data[$i]->id == $data[$i + 1]->id) {
                $name[$data[$i]->lang] = $data[$i]->title;
            } else {
                $name[$data[$i]->lang] = $data[$i]->title;
                $data[$i]->title = $this->formJson($name);

                if (! isset($data[$i]->slug)) {
                    $data[$i]->slug = Str::slug($name['uk']);
                }
                switch ($table) {
                    case 'categories':
                        $this->insertCategory($data[$i]);
                        break;
                    case 'characteristics':
                        $this->insertCharacteristic($data[$i]);
                        break;
                    case 'characteristic_options':
                        $this->insertOption($data[$i]);
                        break;
                }
                $name = [];
            }
        }
    }

    private function insertCategory($data): void
    {
        Category::create([
            'id' => $data->id,
            'title' => $data->title,
            'parent_id' => $data->parent_id == 0 ? 1 : $data->parent_id,
            'slug' => $data->slug,
            'is_active' => 1,
        ]);
    }

    private function insertCharacteristic($data): void
    {
        try {
            Characteristic::create([
                'id' => $data->id,
                'title' => $data->title,
                'slug' => $data->slug,
                'is_active' => 1,
            ]);
        } catch (Exception $e) {
            Characteristic::create([
                'id' => $data->id,
                'title' => $data->title,
                'slug' => $data->slug.''.$data->id,
                'is_active' => 1,
            ]);
        }
    }

    private function insertOption($data): void
    {
        CharacteristicOption::create([
            'id' => $data->id,
            'title' => $data->title,
            'characteristic_id' => $data->characteristic_id,
            'slug' => $data->slug,
            'is_active' => 1,
        ]);
    }

    private function insertProductCharacteristicOption($data): void
    {
        ProductCharacteristicOption::create([
            'characteristic_id' => $data->characteristic_id,
            'characteristic_option_id' => $data->characteristic_option_id,
            'product_id' => $data->product_id,
        ]);
    }

    private function insertProduct($data): void
    {
        Product::create([
            'id' => $data->id,
            'code' => $data->code,
            'category_id' => $data->category_id ?? null,
            'title' => $data->title,
            'description' => $data->description,
            'short_description' => $data->short_description,
            'price' => $data->price ?? null,
            'price_old' => $data->price_old ?? null,
            'slug' => $data->slug,
            'quantity' => $data->quantity,
            'is_active' => 1,
            'product_status_id' => 1,
        ]);
    }

    private function insertProductPictures($data): void
    {
        Product::where('id', $data->id)->update([
            'picture' => $data->picture,
            'other_pictures' => $data->other_pictures,
        ]);
    }

    private function formJson($array)
    {
        return json_encode([
            'ua' => array_key_exists('uk', $array) ? $array['uk'] : '',
            'ru' => array_key_exists('ru', $array) ? $array['ru'] : '',
            'en' => array_key_exists('en', $array) ? $array['en'] : '',
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
        }
    }
}
