@extends('layouts.mail')

@section('main')
    <p>{{__t('Приветствуем')}}.</p>
    <p>{{__t('Ваш заказ отправлен')}}.</p>

    <p>
    {{__t('Номер ТТН')}}:  {{$order->tracking_num}}<br>
    {{__t('Служба доставки')}}: {{$order->delivery->t('title')}}<br>
    {{__t('Отслеживать посылку по номеру можно по этим ссылкам')}}:<br>
    <a href="https://tracking.novaposhta.ua/#/uk">{{__t('Новая почта')}}</a><br>
    <a href="https://track.ukrposhta.ua/tracking_UA.html">{{__t('Укрпочта')}}</a><br>
    {{__t('Будем благодарны, если вы оставите отзыв о работе нашего магазина')}}: <a href="https://g.page/r/CTorR8mkTPXvEBM/review">https://g.page/r/CTorR8mkTPXvEBM/review</a>
    </p>
@stop
