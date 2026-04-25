<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="UTF-8">
    <title>{{ __t('Подробности заказа №') }}{{ $order->id }}</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,800,900&amp;subset=cyrillic"
          rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            color: #222222;
        }

        .line {
            width: 46px;
            height: 2px;
            background-color: #ff0000;
            margin: 10px auto;
        }

        .header_table,
        .info_table,
        .details_table,
        .delivery_table,
        .contact_table,
        .links_table,
        .footer_table {
            text-align: center;
            width: 700px;
            margin: 0 auto;
        }

        .header_table {
            margin-top: 20px;
        }

        .footer_table {
            margin-bottom: 20px;
        }

        .header_table_thanks {
            font-weight: 800;
            padding-bottom: 10px;
            font-size: 18px;
        }

        .header_table_option {
            font-family: 'Mont-ExtraBold', sans-serif;
            font-size: 14px;
            padding: 5px 0;
        }

        .header_table_option span {
            font-weight: 500;
            color: #3e3e3e;
        }

        .info_table {
            border: 1px solid #eaeaea;
            border-radius: 3px;
        }

        .info_table_title {
            font-weight: 900;
            font-size: 14px;
            text-transform: uppercase;
            color: #ff0000;
            background-color: #f0f0f0;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .info_table_title2 {
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
            color: #ff0000;
            background-color: #ffffff;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .info_table_title3 {
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
            color: #ff0000;
            background-color: #f0f0f0;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .details_table td {
            font-size: 13px;
        }

        .product_img {
            width: 80px;
            height: 80px;
            padding: 5px 0;
        }

        .product_name {
            width: 170px;
            padding: 0 5px;
        }

        .product_details td,
        .product_details_2 td {
            font-weight: 500;
        }

        .product_details_2 .product_items {
            font-weight: 800;
            color: #ff0000;
        }

        .info_table_title_footer {
            color: #222;
        }

        .info_table_title_footer span {
            color: #ff0000;
        }

        .details_table {
            border: 1px solid #eaeaea;
            border-radius: 3px;
        }

        .product_wrap {
            border-collapse: collapse;
            border-bottom: 1px solid #eaeaea;
        }

        .delivery_table {
            text-align: left;
        }

        .delivery_table_child {
            width: 430px;
            margin: 0 auto;
            font-size: 11px;
            font-weight: 500;
            color: #272727;
        }

        .delivery_table_child_title {
            font-weight: 800;
            vertical-align: top;
            width: 70px;
        }

        .delivery_table_child_text {
            padding-left: 10px;
        }

        .contact_table .header_table_option {
            font-size: 18px;
            padding-bottom: 0;
        }

        .contact_table .line {
            margin: 20px auto;
        }

        .contact_table {
            width: 290px;
            text-align: center;
        }

        .contact_table_11 {
            font-size: 11px;
            font-weight: 800;
            color: #272727;
            padding-bottom: 20px;
        }

        svg {
            width: 32px;
            height: 32px;
        }

        a {
            display: inline-block;
        }

        .footer_table {
            font-size: 9px;
        }

        @media  screen and (max-width:701px) {
            .header_table,
            .info_table,
            .details_table,
            .delivery_table,

            .links_table,
            .footer_table {
                width: 100%;
            }

            .header_table_thanks {
                font-size: 18px;
                padding-bottom: 15px;
            }

            .details_table td {
                display: block;
                margin: 0 auto;
            }

            img {
                width: 100%;
            }

            .product_name {
                width: 90%;
            }

            .delivery_table_child {
                width: 90%;
            }

            .footer_table {
                font-size: 8px;
            }

            .margin_table td {
                height: 25px;
            }
        }

        .number__txt {
            font-weight: 700;
            font-size: 20px;
        }

        @media  print {

            input,
            textarea {
                border: none;
            }
        }
    </style>
</head>

<body>

<table class="header_table">
    <tr>
        <td class="header_table_thanks">
            <span style="font-size: 16px; ">
                {{ __t('Номер заказа:') }}
                <span style="font-weight: 400">
                    <input type="text" style="width:205px;" class="form-control" name="number" value="{{ $order->id }}" >
                </span>
            </span>
            <br>
            <span style="font-size: 16px; font-weight: 400">
                <b>{{ __t('Дата:') }}</b>
                <span>
                        <input type="text" style="width:130px;" class="form-control" name="number" value="{{ $order->formatDate() }}">
                </span>
            </span>
        </td>
        <td>
            <a href="{{ geturl('/') }}" target="_blank">
                <img style="width:180px;" src="{{ glide(setting('page_header_logo'), ['w' => 182, 'h' => 47]) }}">
                <span style="display: block;font-size: 14px;color: #222; margin-top: 10px; text-decoration: none; font-weight: 700">{{ request()->root() }}</span>
            </a>
        </td>
    </tr>
    <tr>
        <td class="number__txt"></td>
    </tr>
    <tr>
        <td class="header_table_option"></td>
    </tr>
</table>

<table class="info_table" cellspacing="0" cellpadding="0">
    <tr>
        <td class="info_table_title">@if($order->receiver=='other'){{__t('Покупець:')}}@else{{ __t('Получатель:') }}@endif</td>
    </tr>
    <tr>
        <td class="header_table_option"><b>{{ __t('Фамилия, имя:') }}</b> <span>{{ $order->last_name }} {{ $order->first_name }}</span></td>
    </tr>
    <tr>
        <td class="header_table_option"><b>{{ __t('Телефон:') }}</b> <span>{{ $order->phone }}</span></td>
    </tr>
    <tr>
        <td class="header_table_option"><b>{{ __t('Эл. почта:') }}</b> <a href="mailto:{{ $order->email }}">{{ $order->email }}</a></td>
    </tr>
</table>

@if($order->receiver=='other')
    <table class="info_table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="info_table_title">{{ __t('Кто заберет посылку') }}</td>
        </tr>
        <tr>
            <td class="header_table_option"><b>{{ __t('Фамилия, имя:') }}</b> <span>{{ $order->receiver_last_name }} {{ $order->receiver_first_name }} {{ $order->receiver_patronymic_name }}</span></td>
        </tr>
        <tr>
            <td class="header_table_option"><b>{{ __t('Телефон:') }}</b> <span>{{ $order->receiver_phone }}</span></td>
        </tr>
    </table>
@endif

<table class="details_table" cellspacing="0" cellpadding="0">
    <tr>
        <td class="info_table_title" colspan="5">{{ __t('Детали заказа:') }}</td>
    </tr>
    <tr style="background-color: #f4f4f4;">
        <td style="padding: 10px 0;">
            {{ __t('ФОТО') }}
        </td>
        <td style="padding: 10px 0;">
            {{ __t('НАЗВАНИЕ / ХАРАКТЕРИСТИКИ') }}
        </td>
        <td style="padding: 10px 0;">
            {{ __t('ЦЕНА') }}
        </td>
        <td style="padding: 10px 0;">
            {{ __t('КОЛИЧЕСТВО') }}
        </td>
        <td style="padding: 10px 0;">
            {{ __t('СУММА') }}
        </td>
    </tr>
    <!-- Весь товар -->
    @foreach($order->products as $item)
        <tr>
            <td class="product_img">
                <img width="80" src="{{ $item->product->getImgPath(80, 80) }}" alt="{{ $item->product->t('title') }}">
            </td>
            <td class="product_name">
                {{ $item->product->t('title') }}
                <br>
                <b>{{ __t('Артикул:') }}</b> {{ $item->product->getArticle() }}
            </td>
            <td class="product_details" style="font-size: 18px;">
                @money($item->price) {{ setting('currency') }}
            </td>
            <td class="product_details_2" style="font-size: 18px;">
                {{ $item->count }}
            </td>
            <td class="product_details_2" style="color: #ff0000;font-size: 18px;">
                @money($item->price * $item->count) <span>{{ setting('currency') }}</span>
            </td>
        </tr>
    @endforeach
    <!-- КОНЕЦ Весь товар -->
    <tr>
        <td class="info_table_title2 info_table_title_footer" style="padding: 10px" colspan="5" align="right">
            {{ __t('Сумма заказа:') }} <span>@money($order->getAllCost()) <span>{{ setting('currency') }}</span></span>
        </td>
    </tr>
    @if($order->sale)
    <tr>
        <td class="info_table_title2 info_table_title_footer" style="padding: 10px" colspan="5" align="right">
            {{ __t('Скидка') }}: <span>@money($order->getSaleSum()) <span>{{ setting('currency') }}</span></span>
        </td>
    </tr>
    @endif
    @if($order->price_delivery)
        <tr>
            <td class="info_table_title2 info_table_title_footer" colspan="2" align="right"></td>
            <td class="info_table_title2 info_table_title_footer" colspan="3" align="right">
                {{ __t('СТОИМОСТЬ ДОСТАВКИ:') }} <span>@money($order->price_delivery) <span>{{ setting('currency') }}</span></span>&nbsp;
            </td>
        </tr>
    @endif
    <tr>
        <td class="info_table_title3 info_table_title_footer" colspan="2" align="left"></td>
        <td class="info_table_title3 info_table_title_footer" colspan="3" align="right">
            <b>{{ __t('Сумма к оплате:') }} <span>@money($order->getPriceForDocumentsAttribute()) <span>{{ setting('currency') }}</span></span></b>&nbsp;&nbsp;&nbsp;
        </td>
    </tr>


</table>

<table class="delivery_table" cellspacing="0" cellpadding="0">
    <tr>
        <td class="delivery_table_child_title" style="padding-top: 20px;">{{ __t('Доставка:') }}</td>
        <td class="delivery_table_child_text" style="padding-top: 20px;">
            {{ $order->delivery->t('title') }}
        </td>
    </tr>
    <tr>
        <td class="delivery_table_child_title" style="padding-top: 20px;">{{ __t('Оплата:') }}</td>
        <td class="delivery_table_child_text" style="padding-top: 20px;">
            {{ $order->payMethod->t('title') }}
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <p class="header_table_option" style="padding-top: 20px">
                {{ __t('Возврат или обмен товара допускается в течении 14 дней, при условии сохранности
                упаковки, комплектующих и внешнего вида товара - ст. 9 закона Украины "О защите прав потребителей"') }}
            </p>
        </td>
    </tr>
</table>
</body>
</html>
