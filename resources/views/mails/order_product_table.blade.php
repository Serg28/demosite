
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0;padding:0">
    <tbody>
    <tr>
        <td style="margin:0;padding:0">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="min-width:540px; padding:0;margin:0;border-collapse:collapse;border-spacing:0px">
                <tbody>

                <tr>
                    <td colspan="11" height="10" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="11" height="1" style="font-size:0;line-height:0;margin:0;padding:0">
                        <div style="display:inline-block;width:100%;height:1px;background:#f0ebe7"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="11" height="10" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                </tr>
                <tr>
                    <td width="101" align="left" style="font:normal 12px/14px 'Roboto',sans-serif;color:#222222;opacity:0.8;white-space:nowrap;margin:0;padding:0">{{__t('Изображение')}}                                            </td>
                    <td width="5" style="font-size:0;line-height:0">&nbsp;</td>
                    <td width="300" align="left" style="font:normal 12px/14px 'Roboto',sans-serif;color:#222222;opacity:0.8;white-space:nowrap;margin:0;padding:0">{{ __t('Назва товару') }}                                            </td>
                    <td width="5" style="font-size:0;line-height:0">&nbsp;</td>
                    <td width="70" align="right" style="font:normal 12px/14px 'Roboto',sans-serif;color:#222222;opacity:0.8;white-space:nowrap;margin:0;padding:0">{{ __t('Количество') }}                                            </td>
                    <td width="10" style="font-size:0;line-height:0;padding:0;margin:0">&nbsp;</td>
                    <td width="65" align="right" style="font:normal 12px/14px 'Roboto',sans-serif;color:#222222;opacity:0.8;white-space:nowrap;text-align:right;margin:0;padding:0">{{ __t('Ціна') }}
                    <td width="10" style="font-size:0;line-height:0;padding:0;margin:0">&nbsp;</td>
                    <td width="65" align="right" style="font:normal 12px/14px 'Roboto',sans-serif;color:#222222;opacity:0.8;white-space:nowrap;text-align:right;margin:0;padding:0">{{ __t('Скидка') }} </td>
                    <td width="10" style="font-size:0;line-height:0">&nbsp;</td>
                    <td width="80" align="right" style="font:normal 12px/14px 'Roboto',sans-serif;color:#222222;opacity:0.8;white-space:nowrap;text-align:right;margin:0;padding:0">{{ __t('Сумма') }}                                            </td>
                </tr>
                <tr>
                    <td colspan="11" height="10" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="11" height="1" style="font-size:0;line-height:0;margin:0;padding:0">
                        <div style="display:inline-block;width:100%;height:1px;background:#f0ebe7"></div>
                    </td>
                </tr>

                <tr>
                    <td colspan="11" height="20" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                </tr>

                <?php $product_discount = 0; ?>
                @foreach ($order->products as $orderItem)
                    @php
                        //$product_discount += ($orderItem->base_price - $orderItem->price);
                        $product_discount += ($order->discount_amount ?: ($orderItem->base_price - $orderItem->price));
                    @endphp

                <tr>
                    <td width="290" align="left" valign="top" style="margin:0;padding:0" colspan="3">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0;padding:0; @if(!$loop->last) margin-bottom:20px @endif">
                            <tbody>
                            <tr>
                                <td width="60" valign="top" align="center" style="margin:0;padding:0;font-size:0;line-height:0">
                                    <a href="{{ $orderItem->product->getUrl() }}" style="text-decoration:none;width:60px;display: block;" target="_blank">
                                        <img {{--width="105"--}} border="0" src="{{asset($orderItem->product->getImgPath(100, 100))}}" alt="Product" style="border:0;display:block;{{--width:100%;--}}max-width:70px;margin:0;padding:0">
                                    </a>
                                </td>
                                <td width="10" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                                <td width="200" valign="top">
                                    <div style="width:100%;margin:0">
                                        <a href="{{ $orderItem->product->getUrl() }}" style="display:inline-block;margin-bottom:6px;color:#222222;font-size:16px;line-height:26px;text-decoration:none" target="_blank">{{ $orderItem->product->t('title') }}</a>
                                        <div style="display:block;color:#555555;font-size:12px;line-height:14px">{{ __t('Артикул') }}: {{ $orderItem->product->code }}</div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <td width="10" style="font-size:0;line-height:0">&nbsp;</td>
                    <td width="70" align="right" valign="top" style="font:normal 14px/26px 'Roboto',sans-serif;color:#222222;white-space:nowrap;padding:0;margin:0">{{ $orderItem->count }}</td>

                    <td width="10" style="font-size:0;line-height:0;padding:0;margin:0">&nbsp;</td>
                    <td width="70" align="right" valign="top" style="font:normal 14px/26px 'Roboto',sans-serif;color:#222222;white-space:nowrap;text-align:right;margin:0;padding:0">
                        {{ $orderItem->base_price }} ₴
                    </td>
                    <td width="10" style="font-size:0;line-height:0;padding:0;margin:0">&nbsp;</td>
                    <td width="70" align="right" valign="top" style="font:normal 14px/26px 'Roboto',sans-serif;color:#222222;white-space:nowrap;text-align:right;margin:0;padding:0">
                        {{ $orderItem->base_price - $orderItem->price }} ₴
                    </td>
                    <td width="10" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                    <td width="80" align="right" valign="top" style="font:normal 14px/26px 'Roboto',sans-serif;font-weight:700;color:#222222;white-space:nowrap;text-align:right;margin:0;padding:0">
                        {{--{{ $orderItem->price*$orderItem->count  }} ₴ --}}
                        {{$orderItem->total_amount ?: $orderItem->price * $orderItem->count}} ₴
                    </td>
                </tr>
                @endforeach

                <tr style="border-collapse:collapse">
                    <td colspan="11" height="20" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;
                    </td>
                </tr>

                <tr style="border-collapse:collapse">
                    <td colspan="11" height="1" style="font-size:0;line-height:0;margin:0;padding:0">
                        <div style="display:inline-block;width:100%;height:1px;background:#e5e5e5"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="11" height="20" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding:0;margin:0" colspan="4">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <td valign="top" align="left" width="270" style="margin:0;padding:0">
                        <div style="color:#222222;font-size:16px;line-height:22px;text-align:left;width:100%">{{ __t('Общая сумма') }}</div>
                    </td>
                    <td valign="top" align="right" width="270" style="text-align:right">
                        <div style="font:16px/22px 'Roboto',Helvetica,sans-serif;color:#222222;text-align:right;width:100%">{{$order->cost_without_sale}} ₴</div>
                    </td>
                </tr>
                {{--
                    <tr>
                        <td colspan="2" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                    </tr>
                <tr>
                    <td valign="top" align="left" width="270" style="margin:0;padding:0">
                        <div style="color:#222222;font-size:16px;line-height:22px;text-align:left;width:100%">{{ __t('Скидка по дисконтной карте') }}</div>
                    </td>
                    <td valign="top" align="right" width="270" style="text-align:right">
                         <div style="font:16px/22px 'Roboto',Helvetica,sans-serif;color:#222222;text-align:right;width:100%">@money($order->sale_discount_amount) ₴</div>
                    </td>
                </tr>

                    <tr>
                        <td colspan="2" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td valign="top" align="left" width="270" style="margin:0;padding:0">
                            <div style="color:#222222;font-size:16px;line-height:22px;text-align:left;width:100%">{{ __t('Скидка по промокоду') }}</div>
                        </td>
                        <td valign="top" align="right" width="270" style="text-align:right">
                            <div style="font:16px/22px 'Roboto',Helvetica,sans-serif;color:#222222;text-align:right;width:100%">@money($order->sale_promo_amount) ₴</div>
                        </td>
                    </tr>
                 --}}

                {{--@if($product_discount && (!$order->sale_discount && !$order->sale_promo)) --}}
                @if($product_discount)
                    <tr>
                        <td colspan="2" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td valign="top" align="left" width="270" style="margin:0;padding:0">
                            <div style="color:#222222;font-size:16px;line-height:22px;text-align:left;width:100%">{{--{{ __t('Скидка на товары') }}--}}{{__t('Общая скидка')}}</div>
                        </td>
                        <td valign="top" align="right" width="270" style="text-align:right">
                            <div style="font:16px/22px 'Roboto',Helvetica,sans-serif;color:#222222;text-align:right;width:100%">@money($order->getSaleSum()) ₴</div>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td colspan="2" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                </tr>
                <tr>
                    <td valign="top" align="left" width="270" style="margin:0;padding:0">
                        <div style="color:#222222;font-size:16px;line-height:22px;text-align:left;width:100%">{{ __t('Стоимость доставки') }}</div>
                    </td>
                    <td valign="top" align="right" width="270" style="text-align:right">
                        {{--<div style="font:16px/22px 'Roboto',Helvetica,sans-serif;color:#222222;text-align:right;width:100%">{!! ($order->price_delivery) ? $order->price_delivery.' ₴' : strip_tags($order->delivery->t('description')) !!}</div> --}}
                        <div style="font:16px/22px 'Roboto',Helvetica,sans-serif;color:#222222;text-align:right;width:100%">
                            @php
                                $deliveryPrice = $order->getDeliveryPrice()
                            @endphp
                            @if($deliveryPrice!=='free' &&  $deliveryPrice > 0)
                                {{$deliveryPrice}} ₴
                            @else
                                {{$order->getDeliveryDesc()}}
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                </tr>

                @if($order->tax)
                    <tr>
                        <td colspan="2" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td valign="top" align="left" width="270" style="margin:0;padding:0">
                            <div style="color:#222222;font-size:16px;line-height:22px;text-align:left;width:100%">{{--{{ __t('Скидка на товары') }}--}}{{__t('Комісія платіжної системи')}}</div>
                        </td>
                        <td valign="top" align="right" width="270" style="text-align:right">
                            <div style="font:16px/22px 'Roboto',Helvetica,sans-serif;color:#222222;text-align:right;width:100%">@money($order->tax) ₴</div>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td colspan="2" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                </tr>
                <tr>
                    <td valign="top" align="left" width="270" style="margin:0;padding:0">
                        <div style="color:#222222;font-size:16px;line-height:19px;font-weight:700;text-align:left;width:100%">{{ __t('К оплате') }}</div>
                    </td>
                    <td valign="top" align="right" width="270" style="text-align:right">
                        <div style="font:16px/19px 'Roboto',Helvetica,sans-serif;color:#222222;font-weight:900;text-align:right;width:100%">@if(!$order->is_delivery_paid_separately) {{$order->cost + $order->price_delivery}} @else {{$order->cost}}@endif ₴</div>
                    </td>
                </tr>
                <tr style="border-collapse:collapse">
                    <td colspan="11" height="20" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;
                    </td>
                </tr>

                <tr style="border-collapse:collapse">
                    <td colspan="11" height="1" style="font-size:0;line-height:0;margin:0;padding:0">
                        <div style="display:inline-block;width:100%;height:1px;background:#e5e5e5"></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>



    <tr>
        <td style="padding:0;margin:0">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
                <tbody>

                <tr>
                    <td colspan="4" height="20" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;"><strong>{{ __t('Данные покупателя') }}</strong></div>
                    </td>
                </tr>

                <tr>
                    <td colspan="4" height="20" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                </tr>

                <tr>
                    <td valign="top" width="120" style="margin:0;padding:0">
                        <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{__t('Ф.И.О')}}</div>
                    </td>
                    <td width="5" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                    <td valign="top" width="425" style="margin:0;padding:0">
                        <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{$order->last_name}} {{ $order->first_name }} </div>
                    </td>
                    <td width="20" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                </tr>
                <tr>
                    <td valign="top" width="120" style="margin:0;padding:0">
                        <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">Email</div>
                    </td>
                    <td width="5" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                    <td valign="top" width="425" style="margin:0;padding:0">
                        <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{ $order->email }}</div>
                    </td>
                    <td width="20" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                </tr>
                <tr>
                    <td valign="top" width="120" style="margin:0;padding:0">
                        <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{__t('Телефон')}}</div>
                    </td>
                    <td width="5" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                    <td valign="top" width="425" style="margin:0;padding:0">
                        <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{ $order->phone }} @if($order->call_me){{__t('Перезвоните мне')}} @else{{__t('Звонить не обязательно')}}@endif</div>
                    </td>
                    <td width="20" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                </tr>
                @if($order->receiver=='other')
                <tr>
                    <td valign="top" width="120" style="margin:0;padding:0">
                        <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{__t('Забирает посылку')}}</div>
                    </td>
                    <td width="5" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                    <td valign="top" width="425" style="margin:0;padding:0">
                        <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{$order->receiver_last_name}} {{$order->receiver_first_name}} {{$order->receiver_patronymic_name}} {{ $order->receiver_phone }}</div>
                    </td>
                    <td width="20" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                </tr>
                @endif

                @if ($order->delivery)

                    @if ($order->city)
                    <tr>
                        <td valign="top" width="120" style="margin:0;padding:0">
                            <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{ __t('Город') }}</div>
                        </td>
                        <td width="5" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                        <td valign="top" width="425" style="margin:0;padding:0">
                            <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{$order->city->t('title')}}</div>
                        </td>
                        <td width="20" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="4" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                    </tr>
                    @endif
                    <tr>
                        <td valign="top" width="120" style="margin:0;padding:0">
                            <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{ __t('Адрес') }}</div>
                        </td>
                        <td width="5" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                        <td valign="top" width="425" style="margin:0;padding:0">
                            <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{ $order->pickUpTheGoods() }}</div>
                        </td>
                        <td width="20" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="4" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td valign="top" width="120" style="margin:0;padding:0">
                            <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{ __t('Способ доставки') }}</div>
                        </td>
                        <td width="5" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                        <td valign="top" width="425" style="margin:0;padding:0">
                            <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{!!  $order->delivery->t('title') !!}</div>
                        </td>
                        <td width="20" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="4" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                    </tr>
                @endif

                @if ($order->payMethod)
                    <tr>
                        <td valign="top" width="120" style="margin:0;padding:0">
                            <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{ __t('Способ оплаты') }}</div>
                        </td>
                        <td width="5" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                        <td valign="top" width="425" style="margin:0;padding:0">
                            <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{!! $order->payMethod->t('title') !!}</div>
                        </td>
                        <td width="20" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="4" height="10" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                    </tr>
                @endif

                @if ($order->comment)
                    <tr>
                        <td valign="top" width="120" style="margin:0;padding:0">
                            <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{ __t('Комментарий') }}</div>
                        </td>
                        <td width="5" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                        <td valign="top" width="425" style="margin:0;padding:0">
                            <div style="color: #222222;font-size: 16px;line-height: 22px;text-align: left;width: 100%;">{{$order->comment}}</div>
                        </td>
                        <td width="20" style="font-size:0;line-height:0;margin:0;padding:0">&nbsp;</td>
                    </tr>
                @endif

                <tr>
                    <td colspan="4" height="20" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
                </tr>
                <tr style="border-collapse:collapse">
                    <td colspan="4" height="1" style="font-size:0;line-height:0;margin:0;padding:0">
                        <div style="display:inline-block;width:100%;height:1px;background:#e5e5e5"></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="5" height="20" style="font-size:0;line-height:0;width:100%;margin:0;padding:0">&nbsp;</td>
    </tr>

    </tbody>
</table>
