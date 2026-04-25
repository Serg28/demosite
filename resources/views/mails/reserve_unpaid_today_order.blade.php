@extends('layouts.mail')

@section('main')
    <p>Заказ {{$order->order_number}} не оплатили и срок резерва истекает сегодня, в {{$order->reserved_to}}</p>
@stop
