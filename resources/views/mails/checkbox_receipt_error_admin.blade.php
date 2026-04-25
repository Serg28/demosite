@extends('layouts.mail')

@section('main')

    <p>{!! str_replace('[number]', $order->order_number, __t('Під час фіскалізації замовлення №[number] виникла помилка чеку'))!!}</p>
    <p><a href="{{$order->getSingleUrl()}}" target="_blank">{{__t('Посилання на замовлення')}}</a></p>

@stop
