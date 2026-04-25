<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

trait ViewedTrait
{
    public function setView(): void
    {
        $nameClass = strtolower(get_class($this));
        if (isset($this->id) && $this->id) {
            $idPage = $this->id;
            $nameCookie = md5($nameClass.'_viewed');
            //$cookies = json_decode(Cookie::get($nameCookie), true);
            $cookies = Cookie::get($nameCookie);

            if ($cookies !== null) {
                $cookies = json_decode($cookies, true);
            } else {
                $cookies = [];
            }

            if (is_array($cookies)) {
                array_unshift($cookies, $idPage);
                $cookies = array_slice($cookies, 0, 16);
            } else {
                $cookies[] = $idPage;
            }

            $cookies = array_unique($cookies);
            Cookie::queue($nameCookie, json_encode($cookies), 100000);
        }
    }

    public function getView(): ?Collection
    {
        $nameClass = strtolower(get_class($this));
        $nameCookie = md5($nameClass.'_viewed');

        //$cookies = json_decode(Request::cookie($nameCookie), true);
        $cookies = Request::cookie($nameCookie);

        if ($cookies !== null) {
            $cookies = json_decode($cookies, true);
        }

        if (is_array($cookies) && count($cookies)) {
            $implodeViewedProductsIds = implode(',', $cookies);

            $products = $nameClass::with(['labels', 'status', 'category.parent'])->whereIn('id', $cookies)
                ->cardFields()
                ->active()
                //->available()
                ->orderByRaw("FIELD(id, $implodeViewedProductsIds)")
                ->rememberForever()->cacheTags(['products'])
                ->get();

            if (isset($this->id)) {
                $products = $products->where('id', '!=', $this->id);
            }

            return $products;
        }

        return null;
    }
}
