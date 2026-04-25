<?php

namespace App\Observers;

use App\Jobs\Order\CreateCheckboxReceiptOrderJob;
use App\Jobs\Order\MonoPayPartsConfirmOrderJob;
use App\Jobs\Order\SendMailToUserCancelledLetterJob;
use App\Jobs\Order\SendMailToUserDisbandedLetterJob;
use App\Jobs\Order\SendMailToUserLetterJob;
use App\Jobs\Order\SendMailToUserReadyDeliveryLetterJob;
use App\Jobs\Order\SendMailToUserRequisitesLetterJob;
use App\Jobs\Order\SendMailToUserRequisitesPaidLetterJob;
use App\Jobs\Order\SendMailToUserSamovivozLetterJob;
use App\Jobs\Order\SendSmsOrderJob;
use App\Jobs\Order\UpdateOrderJob;
use App\Models\CumulativeDiscount;
use App\Models\LegalEntitiesRecipient;
use App\Models\Order;
use App\Services\PromApi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\UserService;

class OrderObserver
{
    private $fields = [];

    //private $start_num_order = 77064;

    //После обновления нового заказа
    public function updated(Order $order): void
    {
        //Накопительная скидка
        if ($order->user) {
            $cost = $order->user->orders()->where('order_status_id', '7')->sum('cost'); //Виконаний
            $discount = $this->getDiscount($cost);
            $order->user->discount_cumulative = $discount;
            $order->user->save();
        }

        //Логика при изменении заказа
        //if (($order->isDirty() || $order->products_updated == 1) && $this->orderLogic($order)) {
        if ($this->orderLogic($order)) {
            //$order->updateQuietly($this->fields); //Чтобы не зацикливалось
            UpdateOrderJob::dispatch($order, $this->fields);
        }

        if ($order->prom_id && $order->status->name_for_prom) {
            (new PromApi())->changeStatus($order->status->name_for_prom, $order->prom_id);
        }
    }

    //После создания нового заказа
    public function created(Order $order): void
    {
        //Прописываем Юрлицо по-умолчанию, если не указано
        if (!$order->legal_entities_recipient_id) {
            $defaultRequisitesId = LegalEntitiesRecipient::default()->value('id');
            $this->changeOrderField($order, 'legal_entities_recipient_id', $defaultRequisitesId);
        }
        //Логика после создания нового заказа
        if ($this->orderLogic($order)) {
            UpdateOrderJob::dispatch($order, $this->fields);
        }
    }

    //Просчет накопительной скидки
    private function getDiscount(float $costAll): float
    {
        $discounts = CumulativeDiscount::orderBy('discount', 'desc')->pluck('discount', 'total_sum')->toArray();

        foreach ($discounts as $cost => $discount) {
            if ($costAll > $cost) {
                return $discount;
            }
        }

        return 0;
    }

    //Изменение поля заказа
    private function changeOrderField($order, $field, $value)
    {
        if ($field && $order) {
            $this->fields[$field] = $value;
            $order->$field = $value;

            return true;
        }

        return false;
    }

    //Обработка логики Заказа
    private function orderLogic(Order $order) //TODO: часть логики можно вынести в сервис App\Services\Order
    {
        //---------
        //00. Логика причины расформирования заказа
        //Если статус заказа НЕ Расформирован, обнуляем причины расформирования
        /*if ($order->order_status_id !== 9) { //НЕ Расформирован
            $this->changeOrderField($order, 'cancel_reason_id', 0);
            $this->changeOrderField($order, 'cancel_reason', '');
        }
        //Если причина расформирования НЕ Другое, обнуляем текстовое описание причины
        if ($order->cancel_reason_id !== 1) { //Причина расформ. НЕ Другой
            $this->changeOrderField($order, 'cancel_reason', '');
        }*/
        //---------

        //---------
        //01. Логика при отсутствии пользователя у заказа
        //Если юзер не заполнен, ищем его в БД по указанному телефону/почте и выбираем его.
        //Если не находим, создаем нового из данных в заказе - только если все нужные поля есть
        if(!$order->user_id &&
            //!empty($order->email) &&
            !empty($order->first_name) &&
            !empty($order->last_name) &&
            !empty($order->phone)
        ) {
            //$user = (new UserService())->registration($order);
            $user = (new UserService())->fastRegistration($order);
            if($user && $user->id) {
                $this->changeOrderField($order, 'user_id', $user->id);
            }
        }
        //---------

        //0. Логика при обновлении товаров заказа
        //Товары заказа отслеживаются в OrderProductsObserver
        /*if ($order->products_updated == 1) {
            $waiting1c = (!empty($order->user_id)) ? 1 : 0;
            $this->changeOrderField(
                $order,
                'waiting_1c',
                $waiting1c
            ); //Выставляем галочку Передать в 1С (но если пользователь не определен, ставим 0, чтобы не выгружать в 1С, пока админ не выставит пользователя)
            $this->changeOrderField($order, 'products_updated', 0); //Сбрасываем признак измененности товаров заказа
        }*/

        //1. Логика статусов оплаты
        /*if ($order->isDirty('is_online_payed')) {
            switch ($order->is_paid()) {
                //switch ($order->is_online_payed) {
                case 1: //Сплачене
                    if ($order->pay_method_id == 4) { //Оплата по реквизитам
                        //SMS: Не відправляємо.
                        SendMailToUserRequisitesPaidLetterJob::dispatch($order)->onQueue('shop-emails');
                    }

                    break;
            }
        }*/

        //2. Логика статусов комплектации
        /*if ($order->isDirty('complect_status_id')) {
            switch ($order->complect_status_id) {
                case 2: //Замовлено у постачальника
                    //Есле понадобится, чтобы товары не в наличии на складе Розничный автоматически попадали в Заказ на складе
                    break;
                case 3: //Комплектуется
                    $this->changeOrderField($order, 'order_status_id', 2); //Статус заказа сменяется на Обробляється
                    break;
                case 4: //Укомплектован
                    //Галка Передавать в 1С при изменении состава заказ + Укомплектован.
                    //Поскольку состав заказа отслеживается в OrderProductsObserver и там при любом изменении галочка Передавать в 1С ставится
                    //то здесь нет необходимости выставлять эту галочку
                    //Поэтому закомментируем строку ниже:
                    //$this->changeOrderField($order, 'waiting_1c', 1); //Выставляем галочку Передать в 1С
                    //Установить дату резерва
                    $dateReservedTo = Carbon::now()->addDays(3)->format('Y-m-d H:i:s'); //->toDateTimeString()
                    $this->changeOrderField($order, 'reserved_to', $dateReservedTo);
                    break;
                case 6: //Готов к выдаче
                    //if ($order->delivery_id == 1 && $order->is_paid()) { //Если метод доставки - Самовывоз + Оплачен
                    if ($order->delivery_id == 1) { //Если метод доставки - Самовывоз +  без проверки оплаты
                        $this->changeOrderField($order, 'order_status_id', 4); //Статус заказа сменяется на Самовывоз
                    }
                    break;
            }
        }*/

        //3. Логика статусов заказа
        if (@empty($order->id_old) && $order->status()->value('send_to_1c') == 1) {
            $waiting1c = (!empty($order->user_id)) ? 1 : 0;
            //Выставляем галочку Передать в 1С, если в настройках статуса заказа это указано (но если пользователь не определен, ставим 0, чтобы не выгружать в 1С, пока админ не выставит пользователя)
            $this->changeOrderField($order, 'waiting_1c', $waiting1c);
            $this->changeOrderField($order, 'processed_1c', 0);
        }

        /*if ($order->isDirty('order_status_id')) {
            switch ($order->order_status_id) {
                case 1: //Новый заказ

                    //-----Методы оплаты
                    switch ($order->pay_method_id) {
                        case 4: //Безготівкова оплата за реквізитами
                            SendMailToUserRequisitesLetterJob::dispatch($order)->delay(30)->onQueue('shop-emails'); //Письмо о новом заказе с реквизитами
                            SendSmsOrderJob::dispatch($order, 'sms.order.new_requisites')->onQueue('shop-sms'); //SMS о новом заказе с реквизитами
                            break;
                        default:
                            //SendMailToUserLetterJob::dispatch($order); //Отправляем письмо о новом заказе. Отправляется в app/Providers/EventServiceProvider.php
                            SendSmsOrderJob::dispatch($order, 'sms.order.new')->onQueue('shop-sms'); //SMS о новом заказе
                            break;
                    }
                    //------

                    break;
                case 3: //Готов к отправке
                    if ($order->is_delivery_service() && $order->is_paid()) { //Доставка Службы доставки + Оплачен + Спаковано (ст. комплектации)
                        SendMailToUserReadyDeliveryLetterJob::dispatch($order)->onQueue('shop-emails');
                        SendSmsOrderJob::dispatch($order, 'sms.order.ready_to_delivery')->onQueue('shop-sms'); //SMS готове до відправки
                    }
                    break;
                case 4: //Самовывоз
                    //if ($order->delivery_id == $order->delivery->pickup_id && $order->is_paid()) { //Если метод доставки - Самовывоз + Оплачен
                    if ($order->delivery_id == $order->delivery->pickup_id) { //Если метод доставки - Самовывоз + без проверки оплаты
                        SendMailToUserSamovivozLetterJob::dispatch($order)->onQueue('shop-emails'); //Отправляем клиенту письмо: Готово к выдаче
                        SendSmsOrderJob::dispatch($order, 'sms.order.samovivoz')->onQueue('shop-sms'); //SMS готове до видачі
                    }
                    break;
                case 5: //Відправлений
                    if ($order->is_delivery_service() && $order->pay_method_id == 7) { //Доставка Службы доставки + Оплата Моно-рассрочка
                        Log::info('Observer: START MONO confirmation for order ' . $order->id);
                        MonoPayPartsConfirmOrderJob::dispatch($order)->onQueue('shop-order'); //Запрос в Монобанк - товар выдан
                    }
                    break;
                case 7: //Виконаний
                    if (!$order->is_delivery_service() && $order->pay_method_id == 7) { //Доставка Самовывоз или Курьер + Оплата Моно-рассрочка
                        MonoPayPartsConfirmOrderJob::dispatch($order)->onQueue('shop-order'); //Запрос в Монобанк - товар выдан
                    }
                    if ($order->isNovaPoshtaDelivery() && $order->pay_method_id == 8) { //Новая Почта + Предоплата
                        //CreateCheckboxReceiptOrderJob::dispatch($order)->onQueue('shop-order'); //Создаем фискализацию второго чека
                    }

                    break;
                case 9: //Розформований
                    //SMS: Не відправляємо.
                    SendMailToUserDisbandedLetterJob::dispatch($order)->onQueue('shop-emails');
                    break;

                case 10: //Скасований. Цей статус виставляється лише клієнтом у ЛК (livewire-компонент App/Livewire/Profile/Order/CancelOrder.php)
                    //Але обробляємо статус тут, щоб не порушувати бізнес-процесс
                    SendMailToUserCancelledLetterJob::dispatch($order)->onQueue('shop-emails');
                    break;
            }
        }*/

        //4. Контрольная проверка - запрет выставлять флаги 1С для старых заказов и без юзера
        if (!@empty($order->id_old)) {
            //Если заказ старый, принудительно выставляем флаги, чтобы не обрабатывалось в 1С
            $this->changeOrderField($order, 'waiting_1c', 0);
            $this->changeOrderField($order, 'processed_1c', 1);
        }

        //в конце возвращаем true, если заказ изменен
        return (count($this->fields)) ? true : false;
    }
}
