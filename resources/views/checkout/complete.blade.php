@extends('layouts.default')

@section('seo_tags')
    <title>{{__t('Ваше замовлення прийнято')}}</title>
@stop

@section('internet_google_analitics_head')
    {{-- Universal Analytics --}}
    <script>
        gtag('event', 'purchase', {
            "transaction_id": "{{ $order->id }}",
            "affiliation": "{{ config('app.name') }}",
            "value": "{{ $order->cost }}",
            "currency": "{{ __t('UAH') }}",
            "items": '{!! $universalAnalytics !!}'
        });
    </script>
@stop

@section('internet_marketing_head')
    <script>
        gtag('event', 'purchase', {
            'send_to': '{{ setting('code_dynamic_remarketing_google') }}',
            'value': '{{ $order->cost }}',
            'items': '{!! $dynamicAnalytics !!}'
        });
    </script>
@stop

@section('internet_GA4_head')
    {{-- Передается при создании заказа в Listeners/Order/SendToGA4.php --}}
    @if(empty($order->pay()) || !empty(request()->get('redirect')))
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ecommerce: null});
        window.dataLayer.push({
            'event': 'purchase',
            'ecommerce': {
                'transaction_id': '{{ $order->id }}',
                'affiliation': '{{ $googleGA4User }}',
                'value': '{{ $order->getPriceForDocumentsAttribute() }}',
                'shipping': {!! ($order->price_delivery > 0) ? 100.00 : 0 !!},
                'tax': '{{$order->tax}}',
                //'coupon': '{{$order->promo_code}}',
                'currency': 'UAH',
                //'payment_type': '{{$order->paymentName()}}',
                //'purchase_type': '{!! $order->is_quick ? '1 click' : 'Standard' !!}',
                'items': {!! $googleGA4Analytics !!}
            }
        });
    </script>
    @endif
@stop

@section('main')

    @if(!empty($order->pay()) && !empty(request()->get('redirect')))
        {{-- Если у заказа есть метод оплаты, редиректим на страницу оплаты --}}
        <style>
            body {
                margin: 0;
                padding: 0;
                overflow: hidden;
            }

            #background-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: white; /* Цвет фона */
                z-index: 9998; /* Z-index установлен чуть меньше, чем у прелоадера */
            }

            #preloader-container {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                text-align: center;
                z-index: 9999;
            }

            #preloader {
                border: 8px solid #f3f3f3;
                border-top: 8px solid #67a445;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

        </style>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                setTimeout(function() {
                    window.location.href = "{{$order->urlPayment()}}";
                }, 2000);
            });
        </script>

        <div id="background-overlay"></div>

        <div id="preloader-container">
            <div id="preloader"></div>
        </div>


    @else


    <div class="thanks pt-40 pb-80">
        <div class="container">
            <div class="top-row">
                <h2 class="fsz-28 fw-600 heading">
                    {!! str_replace('[num]', $order->order_number, __t('Дякуємо, замовлення <br> <a href="" class="color--blue">№[num]</a> прийнято!')) !!}</h2>
                <p class="mt-24">{!! str_replace('[link]', '#',__t('Наш менеджер зв\'яжеться з вами найближчим часом для уточнення деталей замовлення <br>
                    Інформацію про статус замовлення можна відстежити в <a href="[link]" class="color--blue">особистому кабінеті</a>')) !!}</p>
                <div class="thanks__wrap flex v--stretch h--between mt-24">
                    <div class="content br--br-4 bg--white p-24 flex fd--column">
                        <p class="fsz-18 fw-600">{{__t('Ваші данні')}}</p>
                        <div class="info flex fd--column">

                            @if($order->first_name || $order->last_name)
                            <div class="row flex v--center h--between">
                                <p class="color--gray">{{__t('Прізвище та ім\'я')}}</p>
                                <p>{{$order->first_name ?? ''}} {{$order->last_name ?? ''}}</p>
                            </div>
                            @endif

                            @if($order->phone)
                            <div class="row flex v--center h--between">
                                <p class="color--gray">{{__t('Телефон')}}</p>
                                <a href="" class="color--black">{{$order->phone ?? ''}}</a>
                            </div>
                            @endif

                            @if($order->email)
                            <div class="row flex v--center h--between">
                                <p class="color--gray">{{__t('Пошта')}}</p>
                                <a href="mailto:{{$order->email ?? ''}}" class="color--black">{{$order->email ?? ''}}</a>
                            </div>
                            @endif

                            @if ($order->city)
                            <div class="row flex v--center h--between">
                                <p class="color--gray">{{__t('Місто')}}</p>
                                <p>{{$order->city->t('title')}}</p>
                            </div>
                            @endif
                        </div>

                        @if ($order->delivery)
                        <div class="other-info">
                            <p class="color--gray">{!!  $order->delivery->t('title') !!}</p>
                            <p class="mt-8">{{ $order->pickUpTheGoods() }}</p>
                            @if($order->receiver==='other')
                                <p class="mob-br">{{__t('Отримувач')}}: {{$order->receiver_last_name}} {{$order->receiver_first_name}} {{$order->receiver_patronymic_name}}, <br>
                                    <a href="tel:{{ $order->receiver_phone }}" class="color--black">{{ $order->receiver_phone }}</a>
                                </p>
                            @else
                                <p class="mob-br">{{__t('Отримувач')}}: {{$order->last_name}} {{$order->first_name}}, <br>
                                    <a href="tel:{{ $order->phone }}" class="color--black">{{ $order->phone }}</a>
                                </p>
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="side-bar br--br-4 bg--white p-16">
                        <p class="fsz-18 fw-600">{{__t('Ваше замовлення')}}</p>
                        <div class="prod-images flex v--center h--wrap">

                            <?php $product_discount = 0; ?>
                            @forelse($order->products as $product)
                                @php
                                    //$product_discount += ($orderItem->base_price - $orderItem->price);
                                    $product_discount += ($order->discount_amount ?: ($product->base_price - $product->price));
                                @endphp

                                <a href="{{$product->product->getUrl()}}" class="img">
                                    <img src="{{glide($product->product->picture, ['w' => 120, 'h' => 120])}}" alt="{{e($product->product->t('title'))}}" width="120">
                                </a>

                                {{-- Кол-во товара: $orderItem->count --}}
                                {{-- Базовая цена: $orderItem->base_price  --}}
                                {{-- Скидка (базовая - продажи) $orderItem->base_price - $orderItem->price --}}
                                {{-- Итогова цена: $orderItem->total_amount ?: $orderItem->price * $orderItem->count --}}
                            @empty
                                <p>{{__t('Товарів не знайдено')}}</p>
                            @endforelse
                        </div>
                        <div class="order-info mt-12 flex fd--column">
                            <div class="info-row flex v--center h--between">
                                <p class="fsz-14">{{__t('Сума товарів')}}:</p>
                                <p class="fsz-14">@money($order->cost_without_sale) {{ setting('currency') }}</p>
                            </div>
                            @if($product_discount)
                            <div class="info-row flex v--center h--between">
                                <p class="fsz-14">{{__t('Знижка') }}:</p>
                                <p class="fsz-14">@money($order->getSaleSum()) {{ setting('currency') }}</p>
                            </div>
                            @endif
                            <div class="info-row flex v--center h--between">
                                <p class="fsz-14">{{__t('Доставка')}}:</p>
                                <p class="fsz-14">
                                    @php
                                        $deliveryPrice = $order->getDeliveryPrice()
                                    @endphp
                                    @if($deliveryPrice!=='free' &&  $deliveryPrice > 0)
                                        {{$deliveryPrice}} {{ setting('currency') }}
                                    @else
                                        {{$order->getDeliveryDesc() ?: __t('За тарифами перевізника')}}
                                    @endif
                                </p>
                            </div>
                            {{--
                            <div class="info-row flex v--center h--between">
                                <p class="fsz-14">{{__t('Кешбек')}}:</p>
                                <p class="fsz-13 color--orange fw-600 flex v--center"><img src="/assets/images/cb.svg" alt="">530 ₴</p>
                            </div>
                            --}}
                        </div>
                        <div class="total-price flex v--center h--between pt-16">
                            <p class="fw-600">{{__t('Всього')}}:</p>
                            <p class="fsz-18 fw-600">@if(!$order->is_delivery_paid_separately) {{$order->cost + $order->price_delivery}} @else {{$order->cost}}@endif {{ setting('currency') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif
@stop
{{--@push('footer-styles')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/thanks-page.css')}}">
@endpush --}}

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/thanks-page.min.css')}}">
@endpush
