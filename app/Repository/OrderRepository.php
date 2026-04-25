<?php

namespace App\Repository;

use App\Models\Order;
use Carbon\Carbon;

class OrderRepository
{


    public function getUnpaidReserveTomorrowOrders()
    {
        $date = Carbon::today()->addDays(1); //Дата за день до завершения срока резерва

        return $this->getUnpaidReserveOrders()
            ->whereDate('reserved_to', '>=', $date->startOfDay())
            ->whereDate('reserved_to', '<=', $date->endOfDay())
            //->whereBetween('reserved_to', [$date->startOfDay(), $date->endOfDay()]) //за день до даты резерва;
            ->get();
    }

    public function getUnpickupedOrders()
    {
        $date = Carbon::today()->addDays(1); //Дата за день до завершения срока резерва

        return $this->getReservedOrders()
            //->whereBetween('reserved_to', [$date->startOfDay(), $date->endOfDay()]) //за день до даты резерва;
            ->whereDate('reserved_to', '>=', $date->startOfDay())
            ->whereDate('reserved_to', '<=', $date->endOfDay())
            ->get();
    }


    public function getUnpaidReserveOrders()
    {
        return Order::with(['products'])
            ->where([
                'complect_status_id' => 4, //Статус комплектации Укомплектован
                'pay_method_id' => 4, //Метод оплаты - по реквизитам
            ])->where(function ($query) { //НЕ Оплачен
                $query->where('is_online_payed', '=', null)
                    ->orWhere('is_online_payed', '=', '')
                    ->orWhere('is_online_payed', '=', 0);
            });
    }


    public function getExpiresTodayOrders()
    {
        return $this->getReservedOrders()
            ->whereDate('reserved_to', Carbon::today())
            ->get();
    }

    public function getUnpaidReserveTodayOrders()
    {
        return $this->getUnpaidReserveOrders()
            ->whereDate('reserved_to', Carbon::today())
            ->get();
    }

    public function getReservedOrders()
    {
        return Order::with(['products'])
            ->where([
                'delivery_id' => 1, //Самовывоз
                'order_status_id' => 4, //Статус заказа - Самовывоз
                'complect_status_id' => 6, //Статус комплектации - Готов к выдаче
                'is_online_payed' => 1, //Оплачен
            ]);
    }

    //Запрашиваем заказы с Новой почтой, оплаченные и упакованные
    private function getNovaPoshtaOrders()
    {
        return Order::with(['products'])
            ->where([
                //'order_status_id' => 3, //Статус заказа - Готов к отправке
                'complect_status_id' => 5, //Статус комплектации - Упакован
                'is_online_payed' => 1, //Оплачен
            ])
            ->whereIn('delivery_id', [2, 9]) //Новая Почта (в отделение + адресная)
            ->excludeCompletedAndCancelled()
            ->whereNotNull(['np_info', 'tracking_num']); //Есть трекномер + данные ТТН
    }

    //Все заказы c Новой почтой, оплаченные и упакованные
    public function getAllNovaPoshtaOrders()
    {
        return $this->getNovaPoshtaOrders()->get();
    }

    //Все заказы c Новой почтой, готовые к отправке
    public function getReadyToDeliveryOrders()
    {
        return $this->getNovaPoshtaOrders()
            ->where([
                'order_status_id' => 3, //Статус заказа - Готов к отправке
            ])
            ->get();
    }

    //Все заказы c Новой почтой, которые уже отправлены и в дороге
    public function getSentOrders()
    {
        return $this->getNovaPoshtaOrders()
            ->where([
                'order_status_id' => 5, //Статус заказа - Отправлен
            ])
            ->get();
    }

    //Все заказы c Новой почтой, которые уже доставлены
    public function getDeliveredOrders()
    {
        return $this->getNovaPoshtaOrders()
            ->where([
                'order_status_id' => 5, //Статус заказа - Отправлен
            ])
            ->get();
    }
}
