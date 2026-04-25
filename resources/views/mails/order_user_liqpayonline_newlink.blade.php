@extends('layouts.mail')

@section('main')
    <p>{{__t('Приветствуем')}}.</p>

    <p>{{__t('Мы обновили состав Вашего заказа. Чтобы оплатить его,')}} <a href="{{route('payment.init',['order'=>$order])}}" target="_blank">{{__t('нажмите здесь')}}</a></p>

    <p><strong>{{__t('Состав Вашего заказа')}} №{{$order->order_number}}:</strong></p>

    @include('mails.order_product_table')

@stop
