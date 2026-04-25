<?php

namespace App\Livewire\Product;

use App\Models\Characteristic;
use App\Models\Product;
use App\Models\ProductCharacteristicOption;
use App\Models\CharacteristicOption;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;

class RelationProducts extends Component
{

    public $page;

    private $characteristics;

    public function mount(Product $page): void
    {
        $this->page = $page;
    }

    /*Новый вариант с совпадениями: учитывает все текущие значения характеристик.
    * Выводит для каждой характеристики другие значения, с учетом всех текущих остальных
     * Ищем похожие товары среди товаров по полю related_code
     * и по указанным в родительской категории хар-кам $productPage->category->characteristicsForProductRelative
    */
    public function buildRelations(): mixed
    {
        //return Cache::tags(['products', 'characteristics', 'characteristic_options', 'сategories'])
        //     ->rememberForever('relative_product_' . $this->page->getCacheKey(), function () {
        $productPage = $this->page;
        $relationProducts = [];
        $products = [];

        $characteristicsRelatives = [];

        //Если у товара есть категория, берем в ней хар-ки, которые нужно выводить как ссылки
        if ($productPage->category) {
            $characteristicsRelatives = $productPage->category->characteristicsForProductRelative->pluck('id');
        }

        if (count($characteristicsRelatives)) {

            //Берем код товара в related_code
            $modelCharacteristic = $productPage->related_code;

            if ($productPage->category_id && $modelCharacteristic) {

                // Используйте query builder для построения запроса к базе данных
                $products = Product::with(["characteristics"])
                    ->leftJoin(
                        "product_characteristic_options",
                        "products.id",
                        "=",
                        "product_characteristic_options.product_id"
                    )
                    //Если нужно искать в рамках текущей категории товара
                    //->where("products.category_id", $productPage->category_id)
                    // Применяем фильтр по related_code, если это необходимо
                    ->where('related_code', $modelCharacteristic)
                    // Применяем пользовательские локальные скоупы для фильтрации активных и доступных продуктов
                    ->active()
                    //->available()
                    //->notNullPrice()
                    // Выбираем только столбец products.id
                    ->select("products.id")
                    // Извлекаем массив идентификаторов продуктов
                    ->pluck("products.id");

                // Добавляем текущую страницу продукта в коллекцию, если нужно
                $products->push($productPage->id);
            }

            $currentProductParams = $productPage->characteristics
                ->whereIn("characteristic_id", $characteristicsRelatives)
                ->pluck("characteristic_option_id", "characteristic_id")
                ->all();

            if ($products) {
                foreach ($characteristicsRelatives as $showOption) {
                    $otherCharacteristics = collect(
                        $currentProductParams
                    )->forget($showOption);
                    if (!isset($currentProductParams[$showOption])) {
                        continue;
                    }
                    //$currentShowOptionValue = $currentProductParams[$showOption];

                    $options = ProductCharacteristicOption::with([
                        "product.characteristics",
                        "characteristicOption",
                        "product:id,quantity,slug"
                    ])
                        ->from('product_characteristic_options  as pco')
                        ->whereIn("pco.product_id", $products)
                        ->where("pco.characteristic_id", $showOption)
                        ->where(function ($query) use ($otherCharacteristics) {
                            foreach ($otherCharacteristics as $charId => $charOption) {
                                $query->whereIn("product_id", function ($subquery) use ($charId, $charOption) {
                                    $subquery
                                        ->select("product_id")
                                        ->from("product_characteristic_options as pco2")
                                        ->where("pco2.characteristic_id", $charId)
                                        ->where("pco2.characteristic_option_id", $charOption);
                                });
                            }
                        })
                        ->get();

                    $options = $options->sortBy(function ($option) {
                        return $option->characteristicOption->title;
                    });

                    foreach ($options as $option) {
                        //$title = __t('Виберіть ' . mb_strtolower($option->characteristic->t('title')));
                        $title = $option->characteristic->t('title');
                        $item = [
                            'title' => $option->characteristicOption->t('title'),
                            'color' => $option->characteristicOption->color ?? '',
                            'product' => $option->product,
                        ];

                        $relationProducts[$title] = $relationProducts[$title] ?? [];

                        // Проверяем, что 'title' не было добавлено ранее
                        if (!array_key_exists($item['title'], $relationProducts[$title]) ||
                            ($option->characteristic_option_id === $currentProductParams[$option->characteristic_id] &&
                                $option->product->id === $productPage->id)
                        ) {
                            $relationProducts[$title][$item['title']] = $item;
                        }

                        $this->characteristics[$title] = $option->characteristic->slug;

                    }
                }

            }
        }

        return $relationProducts;
        //});
    }


    public function render()
    {
        return view('livewire.product.relation-products', [
            'relationProducts' => $this->buildRelations()
        ]);
    }
}