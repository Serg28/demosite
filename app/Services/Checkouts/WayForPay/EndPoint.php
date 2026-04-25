<?php

namespace App\Services\Checkouts\WayForPay;

use WayForPay\SDK\Contract\EndpointInterface;


//https://wiki.wayforpay.com/en/view/852102
//Для получения от сервера платежей ссылки на оплату требуется в урл передать ?behavior=offline
//Сервер продавца (или телефон покупателя) отправляет запрос на почту https://secure.wayforpay.com/pay?behavior=offline с полями для ПОКУПКИ .
//В ответ наш сервер, в случае успеха, возвращает {"url": "https: \ / \ / secure.wayforpay.com \ / page? Vkh = 5f6204b5-8300-4cfb-851b-749822d1dba8"}.
//Параметры URL содержат ссылку на оплату, по которой клиент может перейти и получить страницу оплаты.

class EndPoint implements EndpointInterface
{
    public function getUrl()
    {
        return 'https://secure.wayforpay.com/pay?behavior=offline';
    }

    public function getMethod()
    {
        return 'POST';
    }
}
