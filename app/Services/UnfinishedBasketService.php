<?php

namespace App\Services;

use App\Models\UnfinishedBasketsProducts;
use App\Repository\UnfinishedBasketRepository;
use Cart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;

class UnfinishedBasketService
{
    private $currentCookie;

    private const COOKIE_NAME = 'basket_hash';

    private UnfinishedBasketRepository $unfinishedBasketRepository;

    public function __construct()
    {
        $this->unfinishedBasketRepository = app(UnfinishedBasketRepository::class);
    }

    public function saveCookie(): void
    {
        Cookie::queue(self::COOKIE_NAME, $this->getCookie(), 0xFFFFFFF);
    }

    public function getCookie(): string
    {
        if (is_null($this->currentCookie)) {
            $this->currentCookie = Cookie::get(self::COOKIE_NAME, md5(uniqid(rand(), true)));
        }

        return $this->currentCookie;
    }

    public function getProducts(): Collection
    {
        return $this->unfinishedBasketRepository->getProducts($this->getCookie());
    }

    public function deleteCookie(): void
    {
        Cookie::unqueue(self::COOKIE_NAME);
        Cookie::expire(self::COOKIE_NAME);
    }

    public function saveRelationProducts(): void
    {
        $unfinishedBasket = $this->unfinishedBasketRepository->create($this->getCookie());
        $unfinishedBasket->saveProducts($this->getBasketProducts());

        $this->saveCookie();
    }

    public function delete(): void
    {
        $this->unfinishedBasketRepository->delete($this->getCookie());
        $this->deleteCookie();
    }

    public function getBasketProducts(): array
    {
        return Cart::content()->map(function ($cartItem) {
            return new UnfinishedBasketsProducts([
                'product_id' => $cartItem->id,
                'count' => $cartItem->qty,
                'price' => $cartItem->price,
            ]);
        })->all();
    }
}
