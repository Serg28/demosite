<?php
namespace App\Services;

use App\Models\Product as ProductModel;
use App\Jobs\IncrementViewProduct;
use App\Traits\DetectSearchBot;

class Product
{
    use DetectSearchBot;

    protected $product;

    public function __construct(ProductModel $product)
    {
        $this->product = $product;
    }

    public function getPage(): array
    {
        $this->product->load(['status']);

        $this->incrementView();
        $this->product->setView();
        //$otherPictures = $this->getOtherPictures();
        $otherPictures = $this->product->getOtherPictures();
        $params['brand'] = $this->getBrandCharacteristic();
        $guarantee = $this->getGuarantee($params['brand']);
        $deliveriesList = $this->getDeliveriesList();
        $googleGA4product = $this->getGoogleGA4Product();
        //$comments = $this->getComments();
        $isMonoParts = $this->product->isMonobankPayparts;
        $isPrivatParts = $this->product->isPrivatPayparts;
        $allCharacteristics = $this->getAllCharacteristics();

//        $isMonoParts = $product->getMonoPartsCount();
//        $isPrivatParts = $product->getPrivatPartsCount();
        $price = $this->product->getPrice();
        $priceOld = $this->product->getPriceOld();
        $article = $this->product->getArticle();
        $isQuick = $this->isQuick();
        $baseCharacteristics = $this->product->baseCharacteristics;

        $count = $baseCharacteristics->count();
        $lendth = (3 - ($count % 3));
        if($lendth >= 3){$lendth = 0;}
        $additionalCount = $count + $lendth;

        $baseCharacteristics = $baseCharacteristics->pad($additionalCount, ['title' => '', 'values' => '']);
        return [
            'page' => $this->product,
            'otherPictures' => $otherPictures,
            'allCharacteristics' => $allCharacteristics,
            'baseCharacteristics' => $baseCharacteristics,
            'params' => $params,
            'guarantee' => $guarantee,
            'googleGA4product' => $googleGA4product,
            'deliveriesList' => $deliveriesList,
            'comments' => collect(),
            'isMonoParts' => $isMonoParts,
            'isPrivatParts' => $isPrivatParts,
            'price' => $price,
            'priceOld' => $priceOld,
            'article' => $article,
            'isQuick' => $isQuick
        ];
    }

    /*protected function getOtherPictures()
    {
        return cache()->rememberForever('product.otherPictures.' . $this->product->getCacheKey(), function () {
            return $this->product->getOtherImgWithOriginal('other_pictures', ['w' => 155, 'h' => 155]);
        });
    }*/

    protected function getBrandCharacteristic()
    {
        $brandCharacteristic = $this->product->brand;
        return (!empty($brandCharacteristic)) ? $brandCharacteristic : false;
    }

    //Формирование всех характеристик с группировкой по названию группы - аттрибут all_characteristics
    //Значения через запятую, характеристики без группы выводятся внизу
    public function getAllCharacteristics()
    {
        // Группируем характеристики по `group_title`, лениво обрабатываем данные
        $grouped = $this->product->allCharacteristics()
            ->lazy()
            ->groupBy(function ($item) {
                return $item->t("group_title") ?: '_no_group';
            });

        // Преобразуем каждую группу, чтобы значения были через запятую
        $mapped = $grouped->map(function ($group) {
            return $group->groupBy('characteristic_title')->map(function ($items) {
                return [
                    "title" => $items->first()->t('characteristic_title'),
                    //"values" => $items->map(fn($item) => $item->t('option_title'))->implode(", "),
                    "values" => $items->map(fn($item) => !empty($item->t("option_title")) ? $item->t("option_title") : $item->t('characteristic_option_value'))->implode(", "),
                    "option_slug" => $items->first()->option_slug,
                    "characteristic_id" => $items->first()->characteristic_id,
                ];
            })->values();
        });

        // Преобразуем LazyCollection в обычную коллекцию
        $standardCollection = $mapped->collect();

        // Переносим группу без названия в конец
        $noGroup = $standardCollection->pull('_no_group');
        if ($noGroup) {
            $standardCollection->put('_no_group', $noGroup);
        }

        return $standardCollection;
    }

    protected function getGuarantee($brandCharacteristic): bool
    {
        if ($brandCharacteristic && $this->product->category) {
            $guarantee = $this->product->category->guarantee()->active()->with('brand')->whereHas(
                'brand',
                function ($query) use ($brandCharacteristic): void {
                    $query->where('characteristic_option_id', $brandCharacteristic->id);
                }
            )->first();

            return $guarantee !== null;
        }

        return false;
    }

    protected function getDeliveriesList(): array|string|null
    {
        $schedule = $this->product->getDeliveryTime();
        $settingsText = setting('tekst-metody-dostavki-v-kartochke-tovara');
        $paymentDescriptions = [];

        foreach ($schedule as $paymentId => $paymentInfo) {
            $paymentDescriptions["{payment_{$paymentId}}"] = $paymentInfo['description'];
            $paymentDescriptions["{price_{$paymentId}}"] = $paymentInfo['price'];
        }

        return preg_replace('/\{(?:payment|price)_\d+\}/', '', strtr($settingsText, $paymentDescriptions));
    }

    protected function getGoogleGA4Product()
    {
        return (new Analytics(collect([$this->product])))->googleGA4product();
    }

    protected function getComments()
    {
        return $this->product->comments()->with(['user'])->active()->get();
    }

    protected function isQuick()
    {
        return ($this->product->getPrice() >= setting('summa-dlya-dostupnosti-bystrogo-zakaza'));
    }

    private function incrementView(): void
    {
        if(!$this->isSearchBot()) {
            IncrementViewProduct::dispatch($this->product);
        }
    }
}

