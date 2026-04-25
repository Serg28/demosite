<?php

namespace App\Livewire\Product;

use App\Models\Characteristic;
use App\Models\Product;
use App\Models\ProductCharacteristicOption;
use App\Models\CharacteristicOption;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;

class RelationProducts_orig extends Component
{

    public $page;

    public function mount(Product $page): void
    {
        $this->page = $page;
    }

    /*Новый вариант с совпадениями: учитывает все текущие значения характеристик.
    * Выводит для каждой характеристики другие значения, с учетом всех текущих остальных
    */
    public function buildRelations(): mixed
    {
        return Cache::tags(['products', 'characteristics', 'characteristic_options', 'сategories'])
             ->rememberForever('relative_product_' . $this->page->getCacheKey(), function () {
                 $productPage = $this->page;
                 $relationProducts = [];
                 $products = [];

                 //$category = $productPage->category->characteristic_id;
                 //$characteristicsRelatives = $productPage->category->characteristicsForProductRelative->pluck("id");
                 $category = null;
                 $characteristicsRelatives = [];

                 if ($productPage->category) {
                     $category = $productPage->category->characteristic_id;
                     $characteristicsRelatives = $productPage->category->characteristicsForProductRelative->pluck('id');
                 }

                 /*$characteristicsProduct = $productPage->characteristics
                     ->whereIn("characteristic_id", $characteristicsRelatives)
                     ->pluck("characteristic_option_id");*/

                 if ($category && count($characteristicsRelatives)) {
                     $modelCharacteristic = $productPage->characteristics
                         ->where("characteristic_id", $category)
                         ->first();

                     if ($productPage->category_id && $modelCharacteristic) {
                         $products = Product::with(["characteristics"])
                             ->leftJoin(
                                 "product_characteristic_options",
                                 "products.id",
                                 "=",
                                 "product_characteristic_options.product_id"
                             )
                             ->where("products.category_id", $productPage->category_id)
                             ->where("characteristic_id", $modelCharacteristic->characteristic_id)
                             ->where("characteristic_option_id", $modelCharacteristic->characteristic_option_id)
                             ->active()
                             ->available()
                             ->notNullPrice()
                             //Закомментировано в связи с некорр. работой при одной характеристике
                             //->whereHas("characteristics", function ($q) use ($characteristicsProduct) {
                             //    $q->whereIn("characteristic_option_id", $characteristicsProduct);
                             //})
                             ->select(["products.id"])
                             ->get();

                         $products->push($productPage);
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
                             ])
                                 ->from('product_characteristic_options  as pco')


                                 ->whereIn("pco.product_id", $products->pluck("id"))
                                 ->where("pco.characteristic_id", $showOption)
                                 /*->where(
                                     "characteristic_option_id",
                                     "!=",
                                     $currentShowOptionValue
                                 )*/
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

                             /*foreach ($options as $option) {
                                 $relationProducts[__t(
                                     "Виберіть " .
                                     mb_strtolower(
                                         $option->characteristic->t("title")
                                     )
                                 )][] = [
                                     "title" => $option->characteristicOption->t(
                                         "title"
                                     ),
                                     "product" => $option->product,
                                 ];
                             }*/


                             foreach ($options as $option) {
                                 $title = __t('Виберіть ' . mb_strtolower($option->characteristic->t('title')));
                                 $item = [
                                     'title' => $option->characteristicOption->t('title'),
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
                             }





                         }

                         // Добавляем текущие параметры в $relationProducts
                         /*$currentProductParams = array_diff_key(
                             $currentProductParams,
                             $relationProducts
                         );

                         foreach ($currentProductParams as $charId => $charOption) {
                             $characteristic = Characteristic::find($charId);
                             if ($characteristic) {
                                 $characteristicOption = CharacteristicOption::find(
                                     $charOption
                                 );
                                 if ($characteristicOption) {
                                     $relationProducts[__t(
                                         "Виберіть " .
                                         mb_strtolower(
                                             $characteristic->t("title")
                                         )
                                     )][] = [
                                         "title" => $characteristicOption->t("title"),
                                         "product" => $productPage,
                                     ];
                                 }
                             }
                         }*/
                     }
                 }

                 return $relationProducts;
             });
    }


    public function render()
    {
        return view('livewire.product.relation-products', [
            'relationProducts' => $this->buildRelations()
        ]);
    }
}
