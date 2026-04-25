<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="UTF-8">
    <title>{{ __t('Рахунок на оплату') }} № {{ $order->id }}</title>
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

        .header_table2 {
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

        @media screen and (max-width: 701px) {
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
            padding: 0 !important;
        }

        @media print {

            input,
            textarea {
                border: none;
            }
        }

        input, textarea {
            padding: 0 10px;
        }

        table.tableprods {
            border-collapse: collapse;
            border-spacing: 0;
        }

        table.tableprods th {
            text-align: center;
            background: #ccc;
            padding: 5px !important;
            border: 1px solid black;
        }

        tr.product td {
            border: 1px solid black;
        }

        table.tableprods td {
            padding: 5px !important;
            width: max-content;

        }

        .detail_recipient {
            padding: 0 10px 10px 10px;
        }
    </style>
</head>

<body>
<br/>
<div class="block">
    <p style="font-size: 12px; text-align: center">{{__t('Увага! Оплата цього рахунку означає погодження з умовами поставки товарів. Повідомлення про оплату
є обов\'язковим, в іншому випадку не гарантується наявність товарів на складі. Товар відпускається за фактом надходження коштів на п/р Постачальника, самовивозом, за наявності довіренності та паспорта.')}}
    </p>
</div>
<br/>
<br/>
<br/>
<div class="block" style="text-align: left;">
    <p style="font-size: 18px; font-weight: 600;">{{__t('Рахунок на оплату')}}
        <input type="text" style="width:100px; font-size:16px;" class="form-control" name="number"
               value="{{ $order->id }}">
        {{__t('від')}}
        <input type="text" style="width:80px; font-size:16px;" class="form-control" name="number"
               value="{{ $order->formatDate() }}">
    </p>
    <br/>
    <hr>
    <br/>
    <table style="width: 100%;">
        <tr>
            <td valign="top" width="105">
                <b>{{ __t('Постачальник:') }}</b>
            </td>
            <td valign="top">
                <div class="detail_recipient">
                    @if($order->recipient)
                        {!! $order->recipient->t('title')!!}
                        <br/>
                        {!! $order->recipient->t('detail') !!}
                </div>
                @else
                    <textarea rows="4"
                              style="width:100%; font-size:16px;"></textarea>
                @endif
            </td>
        </tr>
        <tr>
            <td valign="top" width="105">
                <b>{{ __t('Покупець:') }}</b>
            </td>
            <td colspan="2" valign="top">
                <input type="text" style="width:90%; margin-top:0px; font-size:16px;" value="{{ $order->name }}">
            </td>
        </tr>
    </table>
</div>
<br/>
<br/>
<br/>
<table class="header_table tableprods">
    <tr class="product">
        <th align="center">№</th>
        <th align="center">{{ __t('Товар') }}</th>
        <th align="center">{{ __t('Кіл-ть') }}<br/>{{ __t('шт.') }}</th>
        <th align="center">{{ __t('Ціна,') }}<br/>{{ setting('currency') }}</th>
        <th align="center">{{ __t('Сума, в') }}<br/>{{ setting('currency') }}</th>
    </tr>
    @foreach($order->products as $item)
        <tr class="product">
            <td align="left">{{ $loop->iteration }}</td>
            <td align="left">{{ $item->product->t('title') }}</td>
            <td align="center">{{ $item->count }}</td>
            <td nowrap align="center">@money($item->price)</td>
            <td nowrap align="center">@money($item->price * $item->count)</td>
        </tr>
    @endforeach
    @if($order->sale)
        <tr class="product">
            <td colspan="4" align="right">
                {{ __t('Скидка') }}
            </td>
            <td>
                @money($order->getSaleSum())
            </td>
        </tr>
    @endif
    @if($order->price_delivery)
        <tr class="product">
            <td colspan="4" align="right">
                {{ __t('Вартість доставки') }}
            </td>
            <td>
                @money($order->price_delivery)
            </td>
        </tr>
    @endif
    <tr>
        <td colspan="4" align="right">
            <b>{{ __t('Всього') }}:</b>
        </td>
        <td align="center">
            <b>@money($order->getPriceForDocumentsAttribute())</b>
        </td>
    </tr>
</table>
<br/>
<br/>
<br/>
<div class="block">
    <p style="font-size: 12px">{{__t('Всього найменувань')}}, {{ count($order->products) }} {{__t('на суму')}}
        @money($order->getPriceForDocumentsAttribute()) {{ setting('currency') }}</p>
    <p style="font-weight: 700;">{{ $order->numberToWords() }}</p>
    <br/>
    <hr>
</div>
<br/>
<br/>
<div class="block" style="text-align: right;">
    {{__t('Виписав(ла)')}} ___________________________________
</div>
</body>
</html>
