<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cookie;

trait AnaliticProductTrait
{
    /*public function getListName()
    {
        return  request()->list ?? (isset(json_decode(Cookie::get('analitic_list_name'), true)[$this->id])? json_decode(Cookie::get('analitic_list_name'), true)[$this->id] : '') ;
    }*/

    public function getListName()
    {
        $cookie = Cookie::get('analitic_list_name');
        $list = null;

        if ($cookie !== null) {
            $decodedCookie = json_decode($cookie, true);
            $list = $decodedCookie[$this->id] ?? null;
        }

        return request()->list ?? $list ?? '';
    }

    /**
     * Формирует данные аналитики GA4 для одного товара
     * @return array
     */
    public function getProductDataAnalitic()
    {
        //$item_brand = $this->getCharacteristic('brand') ? $this->getCharacteristic('brand')->t('title') : '';
        $item_brand = ($this->brand) ? $this->brand->t('title') : '';
        $item_category2 = $this->getParentCategoryName();
        $item_category = $this->getTopCategoryName();

        return [
            'item_name' => $this->t('title'),
            'item_id' => $this->getArticle(),
            'price' => $this->getPrice(),
            'item_brand' => $item_brand,
            'item_category' => $item_category,
            'item_category2' => $item_category2,
            'item_list_name' => $this->getListName(),
        ];
    }
}
