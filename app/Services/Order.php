<?php

namespace App\Services;

use App\Models\Order as OrderModel;
use App\Models\OrderProductOption;
use App\Models\OrderProducts;
use App\Repository\OrderRepository;
use Gloudemans\Shoppingcart\Facades\Cart;

class Order
{
    private $order;

    private OrderRepository $orderRepository;

    public function __construct(OrderModel $order)
    {
        $this->order = $order;
        $this->orderRepository = app(OrderRepository::class);
    }

    public function saveProducts()
    {
        foreach (\Cart::instance('default')->content() as $cartItem) {

            if($cartItem->qty>0) {
                $product = new OrderProducts([
                    /*
                    'product_id' => $cartItem->model->id,
                    'count' => $cartItem->qty,
                    //'price' => $cartItem->price
                    //--
                    'base_price' => $cartItem->price,
                    'discount' => $cartItem->discountRate,
                    'price' => $cartItem->priceTarget,
                    //--
                    */
                    'user_id' => $this->order->user_id ?? null,
                    'product_id' => $cartItem->model->id,
                    'base_price' => $cartItem->price, //базовая цена одной единицы товара
                    'count' => $cartItem->qty, //количество
                    'discount' => $cartItem->getDiscountRate(), //процент скидки
                    'price' => $cartItem->priceTarget,  //$price (total_amount/count) (цена за штуку со скидкой)
                    'base_amount' => $cartItem->baseAmount, //$base_amount сумма базовая (base_price*count)
                    'discount_amount' => $cartItem->discountAmount, //3  (2.5, округляем в большую сторону. Сумма скидки (сумма базовая*процент скидки))
                    'total_amount' => $cartItem->totalAmount  //$total_amount (Сумма с учетом скидки base_amount - discount_amount)
                ]);


                $orderProducts = $this->order->products()->save($product);

                foreach ($cartItem->options as $characteristicId => $optionId) {
                    if ($optionId && $characteristicId && $characteristicId!=='qtyOld') {
                        $orderProducts->options()->save(new OrderProductOption([
                            'characteristic_id' => $characteristicId,
                            'characteristic_option_id' => $optionId,
                        ]));
                    }
                }
            }
        }

        return $this->order;
    }
}
