<?php

namespace App\Services;

use Illuminate\Support\Facades\Cookie;

class Analytics
{
    public function setListName(int $productId): void
    {
        if (! $listName = request()->input('list')) {
            return;
        }

        $cookie = Cookie::has('analitic_list_name')
            ? (json_decode(Cookie::get('analitic_list_name'), true) ?? [])
            : [];

        $cookie[$productId] = $listName;

        Cookie::queue('analitic_list_name', json_encode($cookie), 5000, null, null, false, false);
    }
}
