<?php

namespace App\Enums;

use App\Enums\Traits\ToArrayTrait;

/**
 * Имена полей таблицы заказа
 *
 * Используются как сопоставление названия поля и его текстового представления в таблице истории изменения заказа.
 */
enum OrderFieldNameEnum: string
{
    use ToArrayTrait;

    case order_id = 'Номер заказа';
    case processed_1c = '1с';
    case num = 'Заказ';
    case contact = 'Контактная информация';
    case cost_without_sale = 'Сумма заказа без скидки, грн';
    case promo_code = 'Промокод';
    case sale_promo = 'Скидка промокода, %';
    case sale_discount = 'Скидка дисконтной карты, %';
    case sale = 'Скидка итоговая, %';
    //case cost = 'Сумма заказа, грн';
    case price_delivery = 'Стоимость доставки, грн';
    case is_delivery_paid_separately = 'Доставка оплачивается отдельно';
    case comment = 'Комментарий клиента';
    case call_me = 'Перезвоните мне';
    case note_for_manager = 'Примечание менеджера';
    case system_notes = 'Комментарии из 1С';
    case created_at = 'Дата создания';
    case prom_id = 'Id заказа на Prom';
    case manager_id = 'Менеджер';
    case user_id = 'Клиент';
    case first_name = 'Имя';
    case last_name = 'Фамилия';
    case patronymic = 'Отчество';
    case phone = 'Телефон';
    case email = 'Email';
    case id_old = 'Ид старого заказа';
    case manager_old = 'Менеджер заказа';
    case manager_id_old = 'ID менеджера заказа';
    case where_from_old = 'Откуда заказ';
    case delivery_old = 'Доставка';
    case address_old = 'Адрес старый';
    case receiver = 'Посылку забирает';
    case receiver_first_name = 'Имя получателя';
    case receiver_last_name = 'Фамилия получателя';
    case receiver_patronymic_name = 'Отчество получателя';
    case receiver_phone = 'Телефон получателя';
    case reserved_to = 'Резерв до';
    case order_status_id = 'Статус заказа';
    case cancel_reason_id = 'Причина расформирования заказа';
    case cancel_reason = 'Другая причина расформирования заказа';
    case complect_status_id = 'Статус комплектации';
    case is_online_payed = 'Статус оплаты';
    case tracking_num = 'Трекинг';
    case delivery_id = 'ИД доставки';
    case city_id = 'Город';
    case address = 'Адрес';
    case delivery_pickup_point_id = 'Пункт самовывоза';
    case np_warehouse_id = 'Отделение новой почты';
    case ukrposhta_warehouse_id = 'Отделение укрпочты';
    case justin_warehouse_id = 'Отделение justin';
    case meest_warehouse_id = 'Отделение meest';
    case pay_method_id = 'Метод оплаты';
    case utm_campaign = 'UTM-CAMPAIGN';
    case utm_content = 'UTM-CONTENT';
    case utm_medium = 'UTM-MEDIUM';
    case utm_source = 'UTM-SOURCE';
    case utm_term = 'UTM-TERM';
    case legal_entities_recipient_id = 'Юридична особа для замовлення';
    case cost = 'Заказ изменен<br>Сумма заказа, грн';
    //case cost = 'Сумма заказа, грн';
    case liqpay_order_id = 'ID заказа в LiqPay.<br>Новая ссылка на оплату';
    case liqpay_info = 'LiqPay';
    case privat_payparts_info = 'PrivatPayParts';
}

