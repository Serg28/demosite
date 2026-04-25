<?php

namespace App\Http\Controllers;

use App\Http\Facades\LastModified;
use App\Jobs\IncrementViewProduct;
use App\Models\Product;
use App\Services\Analytics;
use Illuminate\View\View;

class ProductControllerOld extends Controller
{
    public function page(Product $page): View
    {
        $this->incrementView($page);
        $page->setView();
        $analitics = new Analytics(collect([$page]));
        $page->setSeoGroups('product.index');

        $otherPictures = cache()->rememberForever(
            'product.otherPictures.' . $page->getCacheKey(),
            function () use ($page) {
                return $page->getOtherImgWithOriginal('other_pictures', ['w' => 155, 'h' => 155]);
            }
        );

        //$activeOptions = $page->category->characteristics()->active()->where('is_option_product', 1)->orderBy('priority')->get();  //пока не используется

        $characteristics = $page->characteristicsGrouped;
        $brandCharacteristic = $page->brand;
        $params['brand'] = (!empty($brandCharacteristic)) ? $brandCharacteristic : false;

        $guarantee = false;
        if ($brandCharacteristic) {
            $guarantee = $page->category->guarantee()->active()->with('brand')->whereHas(
                'brand',
                function ($query) use ($brandCharacteristic): void {
                    $query->where('characteristic_option_id', $brandCharacteristic->id);
                }
            )->first();
        }

        $schedule = $page->getDeliveryTime();
        $settingsText = setting('tekst-metody-dostavki-v-kartochke-tovara');
        $paymentDescriptions = [];

        foreach ($schedule as $paymentId => $paymentInfo) {
            $paymentDescriptions["{payment_{$paymentId}}"] = $paymentInfo['description'];
            $paymentDescriptions["{price_{$paymentId}}"] = $paymentInfo['price'];
        }

        $deliveriesList = preg_replace('/\{(?:payment|price)_\d+\}/', '', strtr($settingsText, $paymentDescriptions));

        $googleGA4product = $analitics->googleGA4product();

        LastModified::set($page->updated_at);

        $comments = $page->comments()->with(['user'])->active()->get();

        //Выносим сюда логику и некоторые просчеты из шаблона
        $isMonoParts = $page->isMonobankPayparts;
        $isPrivatParts = $page->isPrivatPayparts;
        $price = $page->getPrice();
        $priceOld = $page->getPriceOld();
        $article = $page->getArticle();
        $isQuick = ($page->price >= setting('summa-dlya-dostupnosti-bystrogo-zakaza'));
        //


        return view(
            'product.index',
            compact(
                'page',
                'otherPictures',
                'characteristics', /*'activeOptions', */
                'params',
                'guarantee',
                'googleGA4product',
                'deliveriesList',
                'comments',
                'isMonoParts',
                'isPrivatParts',
                'price',
                'priceOld',
                'article',
                'isQuick',
                'viewedProducts'
            )
        );
    }

    private function incrementView(Product $page): void
    {
        IncrementViewProduct::dispatch($page);
    }

}
