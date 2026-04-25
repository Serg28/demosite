<?php

namespace App\Services;

use App\Jobs\StoreUnfinishedBasket;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Gloudemans\Shoppingcart\Facades\Cart;

class Basket
{
    /**
     * Название действия для аналитики
     * @var string
     */
    private string $action;

    private $cart;

    private UnfinishedBasketService $unfinishedBasketService;

    public function __construct(UnfinishedBasketService $unfinishedBasketService)
    {
        $this->unfinishedBasketService = $unfinishedBasketService;
        $this->cart = \Cart::instance('default');

        //$this->updateAvailabilityAndPrices();
    }

    public function getCart() {
        return $this->cart;
    }

    public function add(Product $product, $name = null, $qty = null, $price = null, $weight = 0, string $jsonOptions = '{}'): JsonResponse
    {
        $options = $jsonOptions ? json_decode($jsonOptions, true, 512, JSON_THROW_ON_ERROR) : [];

        //Analytic
        $this->action = "add";
        $this->product = $product;
        $this->qty = $qty;
        (new Analytics())->setListName($product->id);
        //

        try {
            if ($availability = $this->availableForOrder($product, $qty)) {
                $this->cart->add($product->id, $name, $availability, $price, $weight, $options)->associate($product);

                StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

                return $this->getMessageJsonSuccess("Товар успішно доданий до кошика");
            }

            return $this->getMessageJsonError("Товара в нужном количестве нет в наличии. Добавлено максимально возможное количество");
        } catch (\Exception $e) {
            return $this->getMessageJsonError("Помилка додавання товару до кошика");
        }
    }

    public function update(string $rowIdProduct, $qty = null): JsonResponse
    {
        $this->product = $this->cart->get($rowIdProduct)->model; //получаем товар

        // Аналитика
        $this->qty = $this->getNewCount($rowIdProduct, $qty); //для аналитики (только разница кол-ва)
        (new Analytics())->setListName($this->product->id);
        //

        try {
            //проверка наличия перед добавлением
            $availability = $this->availableForOrder($this->product, $qty, "update");
            $this->cart->update($rowIdProduct, [
                "qty" =>
                    $availability > 0
                        ? $availability
                        : ($availability < 0 ? $qty + $availability : $this->product->getQuantity()),
                //"qty" => max($availability, 0),  ???
                "options" => ['qtyOld' => null]
            ]);

            StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

            return $availability > 0
                ? $this->getMessageJsonSuccess("Товар успішно оновлений")
                : $this->getMessageJsonError("Товара в нужном количестве нет в наличии. Добавлено максимально возможное количество");

        } catch (\Exception $e) {
            return $this->getMessageJsonError("Помилка додавання товару до кошика");
        }
    }

    public function remove(string $idProduct): JsonResponse
    {
        //---- Analytic
        $this->product = $this->cart->get($idProduct)->model; //товар для аналитики
        $this->qty = (int)$this->cart->get($idProduct)->qty; //кол-во удаляемого товара
        $this->action = "remove";
        //-------------

        try {
            $this->cart->remove($idProduct);

            StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

            return $this->getMessageJsonSuccess("Товар успішно видалений з кошика");

        } catch (\Exception $e) {
            return $this->getMessageJsonError("Помилка видалення товару товару з кошика");
        }
    }

    /**
     * Найти товар в корзине вернуть его
     * @param Product $product
     * @return Collection
     */
    public function findItem(Product $product): Collection
    {
        return $this->cart->search(static function ($cartItem, $rowId) use ($product) {
            return $cartItem->id === $product->id;
        });
    }

    /**
     * Количество единиц товара в корзине.
     * @param Product $product
     * @return int - количество. Если нету, то 0
     */
    public function countItem(Product $product): int
    {
        return (int)$this->findItem($product)->value('qty');
    }

    /**
     * Количество доступного для заказа товара - с учетом его кол-ва в корзине
     * @param Product $product
     * @param string $action add|update
     * @param int $qty
     * @return int|mixed
     */
    /*public function availableForOrder(Product $product, int $qty = 0, string $action = 'add'): mixed
    {
        $inCart = $this->countItem($product);
        if ($action === 'update') {
            return ($product->getQuantity() >= (int)$qty && ($product->getQuantity() > $inCart || (int)$qty < $inCart)) ? (int)$qty : $product->getQuantity() - $qty;
        }
        return ($product->getQuantity() >= ((int)$qty + $inCart)) ? (int)$qty : max($product->getQuantity() - $inCart,
            0);
    }*/
    public function availableForOrder(Product $product, int $qty = 0, string $action = 'add'): int
    {
        $inCart = $this->countItem($product);

        if($qty<=0 && !empty($product->getQuantity())) {
            return 1;
        }

        if ($action === 'update') {
            return ($qty > 0 && $product->getQuantity() >= $qty && ($product->getQuantity() > $inCart || $qty < $inCart)) ? (int)$qty : $product->getQuantity() - $inCart;
        }

        return ($product->getQuantity() >= ($qty + $inCart)) ? (int)$qty : max($product->getQuantity() - $inCart, 0);
    }

    //Возвращает массив ИД товаров в корзине
    public function getIdsProductsInCart()
    {
        return $this->cart->content()->map(function ($item, $key) {
            return $item->id;
        })->toArray();
    }

    //Возвращает массив rowId в корзине => кол-во
    public function getQuantitiesForProducts()
    {
        return $this->cart->content()->map(function ($item, $key) {
            return $item->qty;
        })->toArray();
    }

    /**
     * Возвращает модуль количества единиц измененого товара (только разницу между новым и старым значением)
     * Устанавливает тип действия для аналитики (уменьшено или увеличено значение)
     * @param string $idProduct
     * @param $qty
     * @return float|int
     */
    private function getNewCount(string $idProduct, $qty): float|int
    {
        $qty = (float)$qty;
        $cartItem = $this->cart->get($idProduct);
        $this->action = $qty > $cartItem->qty ? "add" : "remove";
        return abs($qty - $cartItem->qty);
    }

    public function getMessageJsonSuccess(string $message, array $productsData = []): JsonResponse
    {
        $response = [
            "status" => "success",
            "message" => __t($message),
            "count" => $this->cart->count(),
            "action" => $this->action
        ];

        if (!empty($productsData)) {
            foreach ($productsData as $product) {
                $response["products"][] = $product->getProductDataAnalitic() + [
                   "quantity" => $this->qty,
                ];
            }
        } else {
            $response["products"] = [
                $this->product->getProductDataAnalitic() + [
                    "quantity" => $this->qty,
                ],
            ];
        }

        return response()->json($response);
    }

    public function getMessageJsonError(string $message): JsonResponse
    {
        return response()->json([
            "status" => "false",
            "message" => __t($message),
            "count" => $this->cart->count()
        ]);
    }

    /**
     * Актуализация наличия, доступности и цен товаров в корзине.
     *
     * Метод проверяет все товары в корзине и выполняет следующие действия:
     * - Если товар не в наличии, устанавливает ему количество в 0.
     * - Если товар в наличии в меньшем количестве, чем в корзине, ставит ему максимально возможное количество.
     * - Если у товара изменилась цена, устанавливает для товара новую цену в корзине.
     *
     * @return bool Возвращает true, если был изменен хотя бы один товар в корзине, в противном случае - false.
     */
    public function updateAvailabilityAndPrices(): bool
    {
        $cartUpdated = false;

        foreach ($this->cart->content() as $cartItem) {
            $product = $cartItem->model;

            // Устанавливаем количество в 0, если товар не в наличии
            if ($cartItem->qty > 0 && !$product->isActive()) {
                $cartItem->qty = 0;
                $cartUpdated = true;
            }

            //Если товар в наличии, но в корзине - 0, ставим 1
            if (empty($cartItem->qty) && $product->isActive()) {
                $cartItem->qty = $cartItem->options['qtyOld'] ?? 1;
                $cartUpdated = true;
            }

            //Если в корзине больше, чем в наличии - ставим макс. наличие
            if ($cartItem->qty>$product->getQuantity()) {
                $cartItem->options = ['qtyOld' => $cartItem->qty];
                $cartItem->qty = $product->getQuantity();
                $cartUpdated = true;
            }

            // Устанавливаем новую цену, если она изменилась
            if ($cartItem->price !== $product->getPrice()) {
                $cartItem->price = $product->getPrice();
                //$cartUpdated = true;
            }
        }

        return $cartUpdated;
    }

}
