@extends('layouts.mail')

@section('main')

    <p>{{__t('Приветствуем')}}.</p>

    <p>{!! __t('Мы обновили состав вашего заказа, с которым вы можете ознакомиться ниже.') !!}</p>

    <p><strong>{{__t('Состав Вашего заказа')}} № {{$order->order_number}}</strong>:</p>

    @include('mails.order_product_table',['order'=>$order])

@stop
