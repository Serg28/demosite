<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="UTF-8">
    <title>{{ __t('Подробности заказа №') }}{{ $order->order_number }}</title>
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

        .block {
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

        .table-products {
            border: 1px solid #000000;
        }

        .tableprods td {
            border: 1px solid #000000;
            padding: 0 !important;
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
                {{ __t('Номер заказу:') }}
                <input type="text" style="width:205px;" class="form-control" name="number" value="{{ $order->order_number }}" >
            </span>
            <br>
            <span style="font-size: 16px; font-weight: 400">
                <b>{{ __t('Дата:') }}</b>
                <input type="text" style="width:130px;" class="form-control" name="number" value="{{ $order->formatDate() }}">
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
<div class="block">
    {{ __t('ФІЗІЧНА ОСОБА-ПІДПРИЄМЕЦЬ') }}
    <input type="text" class="form-control" style="width: 418px; text-transform: uppercase;" name="full_name" value="{{ $order->name }}"><br />
    <input type="text" class="form-control" style="width:100%;" name="store" ><br />
    <textarea style="width:100%;" rows="3"></textarea>
</div>
<br /><br />
<br /><br />

<div class="block" style="text-align:center;">
    {{ __t('ТОВАРНИЙ ЧЕК №') }} <input type="text" class="form-control" name="number" value="{{ $order->order_number }}">
</div>

<div class="block" style="text-align:right;">&laquo;&nbsp;
    <input type="text" class="form-control" style="width:16px;" name="number" value="{{ $order->getInfoFromDate('d') }}">&nbsp;&raquo; &nbsp;&nbsp;&nbsp;
    <input type="text" style="width:16px;" class="form-control" name="number" value="{{ $order->getInfoFromDate('m') }}">&nbsp;&nbsp;&nbsp;
    <input type="text" style="width:32px;" class="form-control" name="number" value="{{ $order->getInfoFromDate('Y') }}"> {{ __t('р.') }}
</div>

<table class="header_table tableprods" style="border-spacing: 0; border-collapse: collapse; border:1px solid #000000;">
    <tr>
        <td><b>{{ __t('Найменування товару') }}</b></td>
        <td><b>{{ __t('Кількість') }}</b></td>
        <td><b>{{ __t('Ціна за одиницю') }}</b></td>
        <td><b>{{ __t('Сума,') }} {{ setting('currency') }}</b></td>
    </tr>
    @foreach($order->products as $item)
        <tr>
            <td>{{ $item->product->t('title') }}</td>
            <td>{{ $item->count }}</td>
            <td>{{$item->price}}</td>
            <td>@money($item->total_amount ?: $item->price * $item->count)</td>
        </tr>
    @endforeach

    <tr>
        <td>&nbsp;</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @if($order->sale)
        <tr>
            <td><b>{{ __t('Скидка') }}</b></td>
            <td>-</td>
            <td>-</td>
            <td>@money($order->getSaleSum())</td>
        </tr>
    @endif
    @if($order->price_delivery)
        <tr>
            <td><b>{{ __t('Стоимость доставки') }}</b></td>
            <td>-</td>
            <td>-</td>
            <td>@money($order->price_delivery)</td>
        </tr>
    @endif
    <tr>
        <td><b>{{ __t('ПДВ не оподатковується') }}</b></td>
        <td>-</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td><b>{{ __t('ВСЬОГО:') }}</b></td>
        <td>{{ $count }}</td>
        <td>-</td>
        <td>@money($order->getPriceForDocumentsAttribute())</td>
    </tr>
</table>

<br />
<div class="block">
    <p>{{ __t('Форма оплати:') }} <input type="text" style="width:565px;" value="{{ __t('Счёт ФОП') . " ". $order->name }}">  </p>
    <p>{{ __t('Дата і час операції (дата і час оплати):') }}
        <input type="text" style="width:70px;" value="{{ $order->formatDate() }}">,
        <input type="text" style="width:16px;" value="{{ $order->getInfoFromDate('H') }}">{{ __t('годин') }}
        <input type="text" style="width:16px;" value="{{ $order->getInfoFromDate('i') }}">{{ __t('хвилин') }}
    </p>
    <p>{{ __t('Всього (словами):') .' '. $order->numberToWords() }}</p>
</div>
<br />
<br />
<div class="block">
        <span style="float:left;">
            <b>{{ __t('Продавець:') }} <input type="text" value="{{$order->name }}"></b>
        </span>
    <span style="float:right;"><b>{{ __t('Підпис_________________') }}</b></span>
</div>

</body>

</html>
