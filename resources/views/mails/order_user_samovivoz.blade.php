@extends('layouts.mail')

@section('main')
    <p>{{__t('Приветствуем')}}.</p>

    <p>{!! str_replace('[address]', str_replace(array('(',')'), '', $order->pickUpTheGoods()), __t('Ваш заказ готов к выдаче в нашем магазине по адресу [address]. Резерв товаров по заказу 3 дня.')) !!}</p>

    {{--
    <p>{{__t('Чтобы проложить маршрут на карте Google, нажмите')}} <a href="https://www.google.com.ua/maps/place/%D0%B2%D1%83%D0%BB.+%D0%9A%D0%BE%D0%BC%D0%B1%D0%B0%D0%B9%D0%BD%D0%B5%D1%80%D1%96%D0%B2,+3%D0%90,+%D0%9A%D0%B8%D1%97%D0%B2/@50.437533,30.442125,17z/data=!3m1!4b1!4m2!3m1!1s0x40d4cc1e0cee120f:0x15d9762271b90e02">{{__t('здесь')}}</a><br>
    {{__t('Просмотреть график работы нашего магазина можно')}} <a href="https://velosiped.com/kontakty">{{__t('здесь')}}</a>.</p>  --}}

@stop
