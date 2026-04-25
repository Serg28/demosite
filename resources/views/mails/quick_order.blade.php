@extends('layouts.mail')

@section('main')
	<p>{{__t('Приветствуем')}}.</p>
	<p>{!! str_replace('[sitename]', config('app.name'), __t('Спасибо, что выбрали [sitename]')) !!}.</p>
	<p>{!! str_replace('[number]', $order->order_number, __t('Ваш заказ №[number] принят. Мы перезвоним Вам, чтобы уточнить все детали.')) !!}</p>

	<p><strong>{{__t('Состав Вашего заказа')}} №{{$order->order_number}}:</strong></p>

	@include('mails.order_product_table')
@stop
