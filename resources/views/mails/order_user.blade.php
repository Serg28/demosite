@extends('layouts.mail')

@section('main')

    <p>{{__t('Приветствуем')}}.</p>
    <p>{!! str_replace('[sitename]', config('app.name'), __t('Спасибо, что выбрали [sitename]')) !!}.</p>
    @if(!empty($order->call_me))
        <p>{!! str_replace('[number]', $order->order_number, __t('Ваше замовлення №[number] прийнято. Очікуйте, будь ласка, з Вами зв\'яжеться наш менеджер для узгодження всіх деталей за вашим замовленням.')) !!}.</p>
    @else
        <p>{!! str_replace('[number]', $order->order_number, __t('Ваше замовлення №[number] прийнято і комплектується. Ми не будемо Вам телефонувати, якщо Ви вказали всі дані в замовленні. Очікуйте на оновлення інформації щодо вашого замовлення.')) !!}.</p>
    @endif
    <p><strong>{{__t('Состав Вашего заказа')}} №{{$order->order_number}}:</strong></p>

    @include('mails.order_product_table')

@stop
