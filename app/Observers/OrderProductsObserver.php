<?php

namespace App\Observers;

use App\Models\OrderProducts;
use Illuminate\Support\Facades\Log;

class OrderProductsObserver
{
    /**
     * Handle the OrderProducts "created" event.
     *
     * @param  \App\Models\OrderProducts  $orderProducts
     * @return void
     */
    public function created(OrderProducts $orderProducts)
    {
        $orderProducts->order->updateQuietly([
            'products_updated' => 1,    //Помечаем в заказе, что товары изменены.
            'waiting_1c' => 1, //Выставляем галочку Передать в 1С - если окно заказа закроют без сохранения
        ]);
    }

    /**
     * Handle the OrderProducts "updated" event.
     *
     * @param  \App\Models\OrderProducts  $orderProducts
     * @return void
     */
    public function updated(OrderProducts $orderProducts)
    {
        //Log::info('Order products update '. $orderProducts->order->id);
        if ($orderProducts->isDirty()) {
            $orderProducts->order->updateQuietly([
                'products_updated' => 1,    //Помечаем в заказе, что товары изменены.
                'waiting_1c' => 1, //Выставляем галочку Передать в 1С - если окно заказа закроют без сохранения
            ]);
        }
    }

    /**
     * Handle the OrderProducts "deleted" event.
     *
     * @param  \App\Models\OrderProducts  $orderProducts
     * @return void
     */
    public function deleted(OrderProducts $orderProducts)
    {
        $orderProducts->order->updateQuietly([
            'products_updated' => 1,    //Помечаем в заказе, что товары изменены.
            'waiting_1c' => 1, //Выставляем галочку Передать в 1С - если окно заказа закроют без сохранения
        ]);
    }

    /**
     * Handle the OrderProducts "restored" event.
     *
     * @param  \App\Models\OrderProducts  $orderProducts
     * @return void
     */
    public function restored(OrderProducts $orderProducts)
    {
        //
    }

    /**
     * Handle the OrderProducts "force deleted" event.
     *
     * @param  \App\Models\OrderProducts  $orderProducts
     * @return void
     */
    public function forceDeleted(OrderProducts $orderProducts)
    {
        //
    }
}
