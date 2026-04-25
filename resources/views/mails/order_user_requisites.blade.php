@extends('layouts.mail')

@section('main')
    <p>{!! __t('Вы выбрали оплату по реквизитам как способ оплаты вашего заказа') !!}
        <strong>№ {{$order->order_number}}</strong><br>
        {{__t('Для оплаты используйте следующие реквизиты')}}</p>

    <div style="width: fit-content;padding: 0 15px 15px 15px;margin: 20px auto; border:1px solid #5b8f43">
        @if($order->legal_entities_recipient_id)
            {!! $order->recipient->t('detail') !!}
        @endif
        {{__t('Назначение платежа')}}: {{__t('Заказ')}} <strong>{{$order->order_number}}</strong><br>
        {{__t('Сумма для оплаты заказа')}}:
        <strong>@if(!$order->is_delivery_paid_separately) {{$order->cost + $order->price_delivery}} @else {{$order->cost}}@endif</strong> {{ setting('currency') }}
        .
    </div>

    <p>{{__t('Если у вас возникли трудности при оплате или вы не знаете, как ее осуществить, воспользуйтесь нашей')}} <a
            href="{{asset(setting('instrukciya-dlya-oplaty-po-rekvizitam'))}}"
            target="_blank">{{__t('краткой инструкцией')}}</a>.</p>

    <p>{!! __t('Обратите внимание! Резерв товаров по заказу – 3 дня. В случае непоступления оплаты в этот срок заказ автоматически расформируется. Если вы хотите продлить этот срок, свяжитесь, пожалуйста, с отделом продаж по контактам в конце этого письма.') !!}</p>

    <p>{!! __t('Ваш заказ будет отправлен в сроки от 1 часа до 3 рабочих дней после получения нами оплаты.') !!}</p>

    <p><strong>{{__t('Состав Вашего заказа')}} № {{$order->order_number}}</strong>:</p>



    @include('mails.order_product_table',['order'=>$order])

@stop
