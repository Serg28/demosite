<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cookie;

trait AnaliticProductTrait
{
    public function getListName(): string
    {
        $cookie = Cookie::get('analitic_list_name');

        if ($cookie !== null) {
            $decoded = json_decode($cookie, true);
            $list = $decoded[$this->id] ?? null;
        }

        return request()->input('list') ?? ($list ?? '');
    }

    public function getProductDataAnalitic(): array
    {
        return [
            'item_id'        => $this->getArticle(),
            'item_name'      => $this->t('title'),
            'price'          => $this->getPrice(),
            'item_list_name' => $this->getListName(),
        ];
    }
}
