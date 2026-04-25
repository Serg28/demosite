<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class Analytics
{
    private $items;

    public function __construct($items = [])
    {
        $this->items = $items;
    }

    public function setItems($items = [])
    {
        $this->items = $items;
    }

    public function universal()
    {
        return $this->items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->t('title'),
                'brand' => env('APP_NAME'),
                'category' => $item->category->t('title'),
                'quantity' => $item->pivot->count,
                'price' => $item->pivot->price,
            ];
        })->toJson(JSON_UNESCAPED_UNICODE);
    }

    public function google()
    {
        return $this->items->map(function ($item) {
            return [
                'item_name' => $item->t('title'),
                'item_id' => $item->id,
                'item_brand' => env('APP_NAME'),
                'item_category' => $item->category->t('title'),
                'quantity' => $item->pivot->count,
                'item_price' => $item->pivot->price,
            ];
        })->toJson(JSON_UNESCAPED_UNICODE);
    }


    //Перенесено в app/Services/GaFourPurchase.php
    /**
     * формирует данные аналитики GA4 о товарах завершенного заказа
     * @return mixed
     */
    public function googleGA4()
    {
        return $this->items->map(function ($item) {
            return [
                'item_name' => $item->t('title'),
                'item_id' => $item->getArticle(),
                'item_brand' => ($item->getCharacteristic('brand')) ? $item->getCharacteristic('brand')->t('title') : '',
                'item_category' => $item->getTopCategoryName(),
                'item_category2' => $item->getParentCategoryName(),
                'quantity' => $item->pivot->count,
                'item_price' => $item->pivot->price,
                'item_list_name' => $item->getListName()
            ];
        })->toJson(JSON_UNESCAPED_UNICODE);
    }

    public function dynamic()
    {
        return $this->items->map(function ($item) {
            return [
                'id' => $item->id,
                'google_business_vertical' => 'retail',
            ];
        })->toJson(JSON_UNESCAPED_UNICODE);
    }

    /**
     * Формирует данные аналитики GA4 для массива переданных товаров
     * Это может быть массив Product или массив товаров в корзине - тогда добавляем quantity
     * @return mixed
     */
    public function googleGA4product()
    {
        return $this->items->map(function ($item) {
            $data = $item instanceof Product ? $item : $item->model;
            $qty = $item instanceof Product ? [] : ['quantity' => $item->qty];
            return $data->getProductDataAnalitic() + $qty;
        })->toJson(JSON_UNESCAPED_UNICODE);
    }

    //Перенесено в app/Services/GA4Purchase.class.php
    // "Card owner" - есть дисконтная карта,
    // “Login user” - если пользователь залогинен, но не регистрировал дисконтную карту,
    // “New user” - если пользователь новый в нашей базе.
    public function googleGA4User($order): string
    {
        if (is_object($order->user)) {
            if ($order->user->discountcard) {
                return 'Card owner';
            }

            if (method_exists($order->user, 'isNew') && $order->user->isNew()) {
                return 'New user';
            }

            return 'Login user';
        }

        return 'Guest user';
    }

    public function getCookie()
    {
        return json_decode(Cookie::get('analitic_list_name'), true);
    }

    /**
     * Устанавливает и запоминает в куке название списка, в котором был товар на момент перехода (клика, добавления в корзину и т.д.)
     * Само значение названия устанавливается через js в куку в файле resources/js/base_new/analitic.js
     * @param $product_id
     * @return string
     */
    public function setListName($product_id)
    {
        if ($list_name = request()->list) {
            $cookie = Cookie::has('analitic_list_name') ? $this->getCookie() : [];
            $cookie[$product_id] = $list_name;
            Cookie::queue('analitic_list_name', json_encode($cookie), 5000, null, null, false, false);
        }
        return $this->getListName($product_id);
    }

    /**
     * Устанавливает и запоминает в куке название списка, в котором был массив товаров на момент перехода (клика, добавления в корзину и т.д.)
     * Само значение названия устанавливается через js в куку в файле resources/js/base_new/analitic.js
     * @param array $product_ids
     */
    public function setMultiListName($product_ids = [])
    {
        if ($list_name = request()->list) {
            $cookie = Cookie::has('analitic_list_name') ? $this->getCookie() : [];
            foreach ($product_ids as $product_id) {
                $cookie[$product_id] = $list_name;
            }
            Cookie::queue('analitic_list_name', json_encode($cookie), 5000, null, null, false, false);
        }
    }

    /**
     * Возвращает название списка, в котором находится товар с момента последнего действии с ним
     * @param $product_id
     * @return string
     */
    public function getListName($product_id)
    {
        return $this->getCookie()[$product_id] ?? '';
    }
}
