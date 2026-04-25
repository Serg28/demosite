@extends('layouts.mail')

@section('main')
    <table align="center" border="0" bgcolor="#ffffff" cellpadding="0" cellspacing="0" width="100%"
           class="wrap-table-email"
           style="margin: 0 auto;max-width: 940px; min-width: 320px;">
        <tbody>
        <tr>
            <td class="wrap-td-content" style="margin: 0;padding: 20px 10px;">
                <table align="center" border="0" bgcolor="#ffffff" cellpadding="0" cellspacing="0" width="100%"
                       class="table-content"
                       style="width: 100%;min-width: 320px;border-collapse: collapse;border-spacing: 0;margin: 0 auto;padding: 0;text-align: center;mso-table-lspace: 0px;mso-table-rspace: 0px;">
                    <tbody>

                    <tr>
                        <td class="table-content__title"
                            style="margin: 0;padding: 20px 0px;font-size: 32px;color: #282c2c;font-weight: 700;">
                            <p
                                style="margin: 0;padding: 20px 0px;font-size: 32px;color: #282c2c;font-weight: 700; text-align: center;">
                                {{ __t('Ваш корзина') }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="table-content__message"
                            style="margin: 0;padding: 20px 0px;font-size: 16px;color: #6b738a;">
                            {{ setting('text_in_letter_of_user_unfinished_basket') }}
                            <br>
                            {{ __t('Завершити своє замовлення можна за посиланням') }}
                            <a href="{{route('checkout')}}" target="_blank"
                               style="color: #e60000;">{{ __t('ваш кошик') }}</a>
                        </td>
                    </tr>

                    <tr>
                        <td class="table-content__message"
                            style="margin: 0;padding: 20px 0px;font-size: 16px;color: #6b738a;">
                            <p style="padding-bottom: 15px; margin: 0;">{{__t("Имя")}}
                                : {{ $unfinishedBasket->user->first_name }}</p>
                            <p style="padding-bottom: 15px; margin: 0;">{{__t('Телефон')}}
                                : {{ $unfinishedBasket->user->phone }}</p>
                            <p style="margin: 0; padding: 0">Email: {{ $unfinishedBasket->user->email }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table align="center" border="0" bgcolor="#ffffff" cellpadding="0" cellspacing="0"
                                   width="100%" class="table-content-order"
                                   style="width: 100%;max-width: 940px;min-width: 320px;border-collapse: collapse;border-spacing: 0;margin: 0 auto;padding: 0;text-align: center;mso-table-lspace: 0px;mso-table-rspace: 0px;table-layout: fixed;
    width:100%;">
                                <thead style="border-top: 2px solid #dee2e6;border-bottom: 2px solid #dee2e6">
                                <tr style="padding: 20px 0px;">
                                    <td class="only-dt" style="padding-left: 10px; padding-right: 10px">
                                        <p
                                            style="font-weight: 700; font-size: 16px;margin: 0; padding: 0;padding: 20px 0px;">
                                        </p>
                                    </td>
                                    <td style="max-width: 25%; padding-left: 10px; padding-right: 10px">
                                        <p
                                            style="font-weight: 700; font-size: 16px;margin: 0; padding: 0;padding: 20px 0px;">
                                            {{ __t('Товар') }}</p>
                                    </td>
                                    <td style="padding-left: 10px; padding-right: 10px">
                                        <p
                                            style="font-weight: 700; font-size: 16px;margin: 0; padding: 0;padding: 20px 0px;">
                                            {{ __t('Стоимость') }}</p>
                                    </td>
                                    <td style="padding-left: 10px; padding-right: 10px">
                                        <p
                                            style="font-weight: 700; font-size: 16px;margin: 0; padding: 0;padding: 20px 0px;">
                                            {{ __t('Количество') }}</p>
                                    </td>
                                    <td style="padding-left: 10px; padding-right: 10px">
                                        <p
                                            style="font-weight: 700; font-size: 16px;margin: 0; padding: 0;padding: 20px 0px;">
                                            {{ __t('Итоговая цена') }}</p>
                                    </td>

                                </tr>
                                </thead>
                                <tbody style="font-weight: 400; font-size: 18px; margin: 0; padding: 0;">
                                @foreach ($unfinishedBasket->products as $product)
                                    <tr style="border-bottom: 1px solid #dee2e6">
                                        <td class="only-dt"><img alt=""
                                                                 src="{{asset($product->product->getImgPath(100, 100))}}"
                                                                 style="padding: 10px 0px;"></td>
                                        <td>
                                            <a style="font-weight: 400; font-size: 16px; margin: 0; padding: 0; color: #e60000;"
                                               href="{{ $product->product->getUrl() }}">{{ $product->product->t('title') }}</a>
                                        </td>
                                        <td>
                                            <p
                                                style="font-weight: 400; font-size: 16px; margin: 0; padding: 0;">
                                                {{ $product->price }} {{ setting('currency') }}
                                            </p>
                                        </td>
                                        <td>
                                            <p
                                                style="font-weight: 400; font-size: 16px; margin: 0; padding: 0;">
                                                {{ $product->count }} {{ __t('шт.') }} </p>
                                        </td>
                                        <td>
                                            <p
                                                style="font-weight: 400; font-size: 16px; margin: 0; padding: 0;">
                                                {{ $product->price*$product->count  }} {{ setting('currency') }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <table align="left" border="0" bgcolor="#ffffff" cellpadding="0" cellspacing="0"
                                   width="100%" class="table-content-order_total"
                                   style="width: 100%;max-width: 940px;min-width: 320px;border-collapse: collapse;border-spacing: 0;margin: 0 auto;padding: 0;text-align: left;mso-table-lspace: 0px;mso-table-rspace: 0px;">
                                <tbody>
                                <tr style="text-align: center;">
                                    <td>
                                        <img alt="" src="{{asset('/images/cart.png')}}" width="100%" height="30px"
                                             style="padding: 10px 0px; text-align: center;max-width: 640px">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table align="left" border="0" bgcolor="#ffffff" cellpadding="0" cellspacing="0"
                                   width="100%" class="table-content-order_total"
                                   style="width: 100%;max-width: 940px;min-width: 320px;border-collapse: collapse;border-spacing: 0;margin: 0 auto;padding: 0;text-align: left;mso-table-lspace: 0px;mso-table-rspace: 0px;border: 1px solid #dee2e6;">
                                <thead style="border-bottom: 1px solid #dee2e6;">
                                <tr>
                                    <td>
                                        <p
                                            style="font-weight: 700; font-size: 16px; margin: 0; padding: 0;padding: 20px 0px 20px 20px;">
                                            {{ __t('Итоги заказа') }}</p>
                                    </td>
                                    <td></td>
                                </tr>
                                </thead>

                                <tbody>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td>
                                        <p
                                            style="font-weight: 400; font-size: 16px; margin: 0; padding: 0;padding: 20px 0px 20px 30px;">
                                            {{__t('Сумма заказа')}}</p>
                                    </td>
                                    <td>
                                        <p
                                            style="font-weight: 400; font-size: 16px; margin: 0; padding: 0;padding: 20px 0px 20px 30px;">
                                                    <span>
                                                        @php
                                                            $total = 0;
                                                            foreach($unfinishedBasket->products as $product){
                                                                $total += $product->price*$product->count;
                                                            }
                                                            echo $total;
                                                        @endphp
                                                        {{ setting('currency') }}
                                                    </span>
                                        </p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>

        </tbody>
    </table>
@stop
