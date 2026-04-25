<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="generator" content="PhpSpreadsheet, https://github.com/PHPOffice/PhpSpreadsheet">
    <meta name="author" content="Lushenius"/>
    <style type="text/css">
        body {
            margin: 0
        }

        html {
            font-family: Calibri, Arial, Helvetica, sans-serif;
            font-size: 11pt;
            background-color: white
        }

        a.comment-indicator:hover + div.comment {
            background: #ffd;
            position: absolute;
            display: block;
            border: 1px solid black;
            padding: 0.5em
        }

        a.comment-indicator {
            background: red;
            display: inline-block;
            border: 1px solid black;
            width: 0.5em;
            height: 0.5em
        }

        div.comment {
            display: none
        }

        table {
            border-collapse: collapse;
            page-break-after: always
        }

        .gridlines td {
            border: 1px dotted black
        }

        .gridlines th {
            border: 1px dotted black
        }

        .b {
            text-align: center
        }

        .e {
            text-align: center
        }

        .f {
            text-align: right
        }

        .inlineStr {
            text-align: left
        }

        .n {
            text-align: right
        }

        .s {
            text-align: left
        }

        td.style0 {
            vertical-align: bottom;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            color: #000000;
            font-family: 'Calibri';
            font-size: 11pt;
            background-color: white
        }

        th.style0 {
            vertical-align: bottom;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            color: #000000;
            font-family: 'Calibri';
            font-size: 11pt;
            background-color: white
        }

        td.style1 {
            vertical-align: bottom;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            font-weight: bold;
            color: #000000;
            font-family: 'Calibri';
            font-size: 18pt;
            background-color: white
        }

        th.style1 {
            vertical-align: bottom;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            font-weight: bold;
            color: #000000;
            font-family: 'Calibri';
            font-size: 18pt;
            background-color: white
        }

        td.style2 {
            vertical-align: bottom;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            font-weight: bold;
            color: #000000;
            font-family: 'Calibri';
            font-size: 11pt;
            background-color: white
        }

        th.style2 {
            vertical-align: bottom;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            font-weight: bold;
            color: #000000;
            font-family: 'Calibri';
            font-size: 11pt;
            background-color: white
        }

        td.style3 {
            vertical-align: bottom;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            font-weight: bold;
            color: #000000;
            font-family: 'Calibri';
            font-size: 12pt;
            background-color: white
        }

        th.style3 {
            vertical-align: bottom;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            font-weight: bold;
            color: #000000;
            font-family: 'Calibri';
            font-size: 12pt;
            background-color: white
        }

        td.style4 {
            vertical-align: bottom;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            font-weight: bold;
            color: #000000;
            font-family: 'Calibri';
            font-size: 14pt;
            background-color: white
        }

        th.style4 {
            vertical-align: bottom;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            font-weight: bold;
            color: #000000;
            font-family: 'Calibri';
            font-size: 14pt;
            background-color: white
        }

        td.style5 {
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid #000000 !important;
            border-top: 1px solid #000000 !important;
            border-left: 1px solid #000000 !important;
            border-right: 1px solid #000000 !important;
            font-weight: bold;
            color: #000000;
            font-family: 'Times New Roman';
            font-size: 11pt;
            background-color: white
        }

        th.style5 {
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid #000000 !important;
            border-top: 1px solid #000000 !important;
            border-left: 1px solid #000000 !important;
            border-right: 1px solid #000000 !important;
            font-weight: bold;
            color: #000000;
            font-family: 'Times New Roman';
            font-size: 11pt;
            background-color: white
        }

        td.style6 {
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid #000000 !important;
            border-top: 1px solid #000000 !important;
            border-left: 1px solid #000000 !important;
            border-right: 1px solid #000000 !important;
            font-weight: bold;
            color: #000000;
            font-family: 'Times New Roman';
            font-size: 7.5pt;
            background-color: white
        }

        th.style6 {
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid #000000 !important;
            border-top: 1px solid #000000 !important;
            border-left: 1px solid #000000 !important;
            border-right: 1px solid #000000 !important;
            font-weight: bold;
            color: #000000;
            font-family: 'Times New Roman';
            font-size: 7.5pt;
            background-color: white
        }

        td.style7 {
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid #000000 !important;
            border-top: 1px solid #000000 !important;
            border-left: 1px solid #000000 !important;
            border-right: 1px solid #000000 !important;
            color: #000000;
            font-family: 'Times New Roman';
            font-size: 11pt;
            background-color: white
        }

        th.style7 {
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid #000000 !important;
            border-top: 1px solid #000000 !important;
            border-left: 1px solid #000000 !important;
            border-right: 1px solid #000000 !important;
            color: #000000;
            font-family: 'Times New Roman';
            font-size: 11pt;
            background-color: white
        }

        td.style8 {
            vertical-align: middle;
            border-bottom: 1px solid #000000 !important;
            border-top: 1px solid #000000 !important;
            border-left: 1px solid #000000 !important;
            border-right: 1px solid #000000 !important;
            color: #000000;
            font-family: 'Times New Roman';
            font-size: 11pt;
            background-color: white
        }

        th.style8 {
            vertical-align: middle;
            border-bottom: 1px solid #000000 !important;
            border-top: 1px solid #000000 !important;
            border-left: 1px solid #000000 !important;
            border-right: 1px solid #000000 !important;
            color: #000000;
            font-family: 'Times New Roman';
            font-size: 11pt;
            background-color: white
        }

        td.style9 {
            vertical-align: bottom;
            text-align: center;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            color: #000000;
            font-family: 'Calibri';
            font-size: 11pt;
            background-color: white
        }

        th.style9 {
            vertical-align: bottom;
            text-align: center;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            color: #000000;
            font-family: 'Calibri';
            font-size: 11pt;
            background-color: white
        }

        table.sheet0 .column0 {
            width: 20pt
        }
        table.sheet0 .column1 {
            width: 50pt;
        }
        /*table.sheet0 col.col1 { width:48.79999944pt }
        table.sheet0 col.col2 { width:227.73333072pt }
        table.sheet0 col.col3 { width:49.47777721pt }
        table.sheet0 col.col4 { width:42pt }
        table.sheet0 col.col5 { width:42pt }
        table.sheet0 col.col6 { width:101.6666655pt }
        table.sheet0 col.col7 { width:42pt }
        table.sheet0 tr { height:15pt }
        table.sheet0 tr.row6 { height:23.25pt }
        table.sheet0 tr.row9 { height:15pt }
        table.sheet0 tr.row11 { height:15pt }
        table.sheet0 tr.row12 { height:15pt }
        table.sheet0 tr.row13 { height:15pt }
        table.sheet0 tr.row14 { height:15pt }
        table.sheet0 tr.row15 { height:15pt }
        table.sheet0 tr.row16 { height:28.5pt }
        table.sheet0 tr.row17 { height:30pt }
        table.sheet0 tr.row18 { height:30pt }
        table.sheet0 tr.row25 { height:18.75pt }*/
        table.sheet0 .column7 {
            width: 40pt
        }

        table.sheet0 td {
            padding: 5px 3px;
        }

        .sitename {
            vertical-align: bottom;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            font-weight: bold;
            color: #000000;
            font-family: 'Calibri';
            font-size: 18pt;
            background-color: white;
            margin-bottom: 2px;
        }

        .sitename span {
            font-size: 85%;
            font-weight: 400;
        }

        .text {
            vertical-align: bottom;
            border-bottom: none #000000;
            border-top: none #000000;
            border-left: none #000000;
            border-right: none #000000;
            color: #000000;
            font-family: 'Calibri';
            font-size: 1em;
            background-color: white;
            line-height: 1.5em;
        }

        .text.phone {
            font-size: 88%
        }

        .text.ordernum {
            font-weight: bold;
            font-size: 1.1em;
            margin-top: 40px;
            margin-bottom: 12px;
        }

        .bold {
            font-weight: bold;
            color: #000000;
            font-family: 'Calibri';
            font-size: 11pt
        }

        .narrow {
            font-family: sans-serif;
        }

        .contacts {
            width: 50%;
            vertical-align: top
        }
    </style>

    <link rel="stylesheet" href="/css/print.css?{{rand(1000,200000)}}" media="print">
</head>

<body>
<style>
    /*@page { margin-left: 0.7in; margin-right: 0.7in; margin-top: 0.75in; margin-bottom: 0.75in; }
    body { margin-left: 0.7in; margin-right: 0.7in; margin-top: 0.75in; margin-bottom: 0.75in; }*/

</style>
<table width="100%">
    <tr>
        <td>
            <table width="100%" class="sheet0" style="margin:0 auto; max-width:1200px">
                <tr>
                    <td colspan="8" style="text-align: center">
                        <img style="margin:0 auto 20px auto; width: 219px; height: 106px;"
                             src="data:image/png;base64,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"
                             border="0"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" style="padding: 0 5px;">
                        <div class="sitename">{{__t('Интернет магазин')}} <span>Velosiped.com</span></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" style="padding: 0 5px;">
                        <div style="display: flex">
                        <div class="contacts left">
                            <div class="text">{{__t('Адрес')}}: {{setting('adres-v-schete')}}</div>
                            <div class="text">{{ __t('Телефон:') }} {{setting('telefony-v-schete')}}</div>
                            <div class="text">Email: {{setting('email-v-schete')}}</div>
                        </div>
                        <div  class="contacts right">
                            <div
                                class="text">@if($order->receiver=='other'){{__t('Покупець:')}}@else{{ __t('Получатель:') }}@endif
                                <span class="bold">
                                @if($order->receiver=='other')
                                        {{ $order->receiver_last_name }} {{ $order->receiver_first_name }} {{ $order->receiver_patronymic_name }}
                                    @else
                                        {{ $order->last_name }} {{ $order->first_name }}
                                    @endif
                            </span>
                            </div>
                            <div class="text">{{ __t('Телефон:') }} <span
                                    class="bold">@if($order->receiver=='other'){{ $order->receiver_phone }}@else{{ $order->phone }}@endif</span>
                            </div>
                            {{--<div class="text">{{ __t('Эл. почта:') }} <span style="font-weight:bold; color:#000000; font-family:'Calibri'; font-size:11pt">{{ $order->email }}</span></div>--}}
                            <div class="text">{{__t('Тип доставки')}}: <span
                                    class="bold">{{ $order->delivery->t('title') }}</span></div>
                            <div class="text">{{__t('Адрес')}}: <span class="bold">{{ $order->pickUpTheGoods() }}</span>
                            </div>
                            @if($order->comment)
                                <div class="text">{{__t('Комментар')}}: <span class="bold">{{$order->comment}}</div>@endif
                        </div>
                        </div>
                    </td>


                </tr>
                <tr>
                    <td colspan="8">
                        <div class="text ordernum"><span
                                class="narrow">{{ $order->created_at }}</span> {{__t('Товарный чек')}} № <span
                                class="narrow">{{ $order->order_number }}</span></div>
                    </td>
                </tr>
            <!--</table>

            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin:0 auto; max-width:1200px"
                   class="sheet0">-->

                <tbody>


                <tr class="row16">
                    <td class="column0 style5 s">№</td>
                    <td class="column1 style5 s">{{__t('Артикул')}}</td>
                    <td class="column2 style5 s">{{__t('Наименование')}}</td>
                    <td class="column3 style5 s">{{__t('Количество')}}</td>
                    <td class="column4 style5 s">{{__t('Ціна')}}</td>
                    <td class="column5 style5 s">{{__t('Скидка')}}</td>
                    <td class="column6 style6 s">{!! __t('Цена со скидкой')!!}</td>
                    <td class="column7 style5 s">{{__t('Сумма')}}</td>
                </tr>
                @foreach($order->products as $item)
                    <tr class="row17">
                        <td class="column0 style7 n">{{$loop->index + 1}}</td>
                        <td class="column1 style8 s">{{ $item->product->getArticle() }}</td>
                        <td class="column2 style8 s">{{ $item->product->t('title') }}</td>
                        <td class="column3 style7 n">{{ $item->count }}</td>
                        <td class="column4 style8 s">{{$item->base_price}}&nbsp;{{ setting('currency') }}</td>
                        <td class="column5 style7 s">{{$item->base_price - $item->price}} {{ setting('currency') }}</td>
                        <td class="column6 style8 s">{{$item->price}}&nbsp;{{ setting('currency') }}</td>
                        <td class="column7 style8 s">{{$item->total_amount ?: $item->price * $item->count}}&nbsp;{{ setting('currency') }}</td>
                    </tr>
                @endforeach
                <tr class="row19">
                    <td class="column0">&nbsp;</td>
                    <td class="column1">&nbsp;</td>
                    <td class="column2">&nbsp;</td>
                    <td class="column3">&nbsp;</td>
                    <td class="column4">&nbsp;</td>
                    <td class="column5">&nbsp;</td>
                    <td class="column6">&nbsp;</td>
                    <td class="column7">&nbsp;</td>
                </tr>
                <tr class="row20">
                    <td class="column0 nopad">&nbsp;</td>
                    <td class="column1 nopad">&nbsp;</td>
                    <td class="column2 nopad">&nbsp;</td>
                    <td class="column3 nopad">&nbsp;</td>
                    <td class="column4 nopad">&nbsp;</td>
                    <td class="column5 nopad">&nbsp;</td>
                    <td class="column6 nopad">&nbsp;</td>
                    <td class="column7 nopad">&nbsp;</td>
                </tr>
                <tr class="row21">
                    <td class="column0 style0 s nopad" colspan="3">{{__t('Менеджер')}}: <span class="bold">{{$order->manager->first_name ?? ''}} {{$order->manager->last_name ?? ''}}</span>
                        <div style="position: absolute;height: 158px;right: 0;width: 100%;"><img
                                style="position: absolute;z-index: 1;left: 0;top: -21px;width: 158px;height: 158px;right: 0;/* width: 100%; */margin: 0 auto;"
                                src="data:image/png;base64,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16mhLVu2nD592gR88sknrXozZ860d99+++2nn37aLBN8qklERISVuXjx4scff9wEtB9xOnPmjJlov9jsOoBrp/Xj4+OtzIEDB7qfQPvggw/s9aFDh9rSbYmKbdu2qQI2rHzWPZU21hcuXGijU6dOLXRJnaCYb0jqicaLpP4pqXV7jx07dsstt+j1Nm3amI8Cy8562wFH9abVj1bCqF7wQw/9vX79/3f27NmQkBDzS7Mra6tQoYLriqjsnOcoK5uzFFWjr7/+uk0QkBMaiI6O1uvdunWzF1NTU6WzvRUTE2OFKN2zCi9fvrxr164auOGGG1STKlUqi+x//OMf999/v1LX++67T2/95S9/McKUddqytC7Zvz6vxA4CaFT2WZlKPI1dm2v2rFn2elxcnC19zJgx9sr27dtdufOHH35oq2Oj1p2fNm2a+4EOJEVSJPUvSV1nYFq1amWHNY1UOxKqt6x/bYcjbfoTJ06YFA8++KDeqlq1qoT66aefrGOuUanqzpDBZOdzDh8+PGXKFOPytttucx0/Vc6r8jdt2mSzuK6IksvGelpamlJR65J/9dVXVhOlupZm1q5dW2/dfPPN9rN6a9assXJM5IyMDC0lKCjoqaee0qjSRnvXMlbFqlWrlIknJCS456oq/O6779aoZpS2gwcPtpp8/vnneveF6Bfcjyy7MutNGzfluugKSZEUSf1CUmPRci45ZQcfN2/ebO/269fPckZ184cNG6Y8VPmpdeddRxXlXVZWll14pCkfeuihDh06WEJao0YNTWkHW3v16hU3Z46cspM24k9v2ZkiZb4afvPNN616FStW7Nmzp5Zo5rZs2TI75zef7V2VqUK0uHLlyi1YsCA752dNjTklp0py9bpmLF269B/+8AetiHXS7VCD0l47fFGqVClNo++Jhx9+2E4r6QvALo/VRhCyf/vb39w7+3ZgVOtuJ9AiIiKsTLs4QbW1YxcPPPCAHWFwXTuFpEiKpH6Uk+7evVuy2Nlq5acuYY8cOWIHQOWUuSantmzZorf++7//2yXUzp071ce3QwGuKUWt/QZys2bNXKfCXafF7beU7eIqddI1rBTSVaDrLPldd91lNinRa9Omjat8K0fdf72lZNbmslnq1asn8ly97wkTJtiw8uXvvvtO0/fv3z/XNQZdunRxiR/wa2hYXrsn4JUrV7ajwJbCly9fPjU1VaO2CKuD2YqkSIqk/iWpKzN96623lPfNmjXL7tp0XVT//fffz5gx44knnlDmpXddvyinHvfkyZPF38cff2z9WaV1sbGxyu80pTr1rsvvz549q75zVFSUClFmOn78+D179tgxykWLFs2cOVP9a9clBMrsXn75ZWWdSgyVKZtcBtO5c+eWLFnSu3fv8PDwSZMm2a+Q2luJiYnq/ut1JbZ2Zn/MmDGaWLOcPn1ai+7evfuXX37pOpWvJcpTLUJvqf6ucrZv3z5ixAj7Eao1a9a4Xpf7qqed4lesXr1ao2/llG8rok03duxYme6TiiEpkiJpgSqZ6/ZQ9+HL74Byv/s+7ynzeDR9rpsy9V9+2XFJO9zpuhQ018Dl1c616FyTud+y5X6v1OXTXP56AR8Oncc0SIqkSOpHkmb/+mjky3+Zw+4ZdUWue0ndn/Scx5SuwnMtwkYts7PzS+qDK5l1FZULqSsW4l5564y7Rt1rdfn0rv/udF7x9Vyv/Dp6/rc2BZIiKZL6qaTFFbme0VenTp1sHwokRVIkRVLPSao81E5tuS7AQlIkRVIkRdLf7emJEye+++47ewITOSmSIqlzqfXAp+Ubq0n4ErVeET6SqyEpkjohLfXV38VDUiRFUiQlkBRJkRRJkRRJkRRJkRRJkRRJkRRJkZRAUiRFUiQlkBRJkRRJkRRJkRRJkRRJkRRJkRRJkZRAUiRFUiQlkBRJPSGpbxDjgbXwikUQzoHST1IQ36AWSZGUQFIkRVIkRVIkRVIkRVIkRVIkRVIkRVICSZEUSZGUQFIkRVIkRVIkRVIkRVIkRVIkRVIkRVICSZEUSZGUQFIkRVIkRVIkRVIvl5S91pd2CMI5Oy35gRflB0iKpASSIimSIimSIimSIimSIimSIimSIimSEkiKpEiKpASSIimSIimSIimSIimSIimSIimSIimSEkiKpEiKpASSIimSIimSIimS+rqkHtjWTrDYK1bTKz5u32h7vvHVy26PpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEjKbo+kSIqkSIqkSIqkSIqkSIqkSIqkSIqkSIqkSIqkSMpuj6RIiqRIiqRIiqRIiqRI6h+SesVeS8NwzheGb3ztsUc5ZzUdsSWRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEnZIZAUSZEUSZEUSZEUSZEUSZEUSZEUSZEUSZEUSZHUxyWFGF8ixjcahm9U0isc9JO2iaRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRI6uuSeoUgTlhN3xCED8s5u71vpCBO2NRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRISvP21CJ8YzV9w8EShF/tUUiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpEiKpN4tKeFL3yh8aXnRF6cTBPENB5EUSZEUSZEUSZEUSZEUSZGUQFIkRVIkRVIkRVIkRVIkRVIkRVIkRVIkRVIkRVIkJZAUSZEUSQkkRVIkRVIkRVIkRVIkRVIkRVIkRVIkvdpIT09fvHjx0KFDW7ZsWbt27QoVKpQrV64Qt29QUNCtt95aq1atNm3aREVFLVmyJDMzkz3G+Y3TT6j1jQ3lG99qXinpoUOHxo0bV6dOnRIej4CAgHvuuScmJiYtLQ1JkZQPC0m9UtLExMTWrVuXLFmyRHGH6tC+fftt27YhKZLyYSGp10i6b98+GVrCeRESEpKamoqkSMqHhaSOlvTChQsTJ04MDAws4dS4/vrrJ02adPHiRSRFUj4sJHWipEePHm3YsGEJb4jg4OBrPB9F40RSJEXSwo+dO3dWqVKlhPfEHXfckZycjKRIyoeFpE6R9LPPPqtQoUIJb4uKFSsmJSUhKZLyYSFp8Uu6e/dub2TU4qabblI2jaRIyoeFpMUpaXp6+u23317Cm6Ny5coZGRlIiqR8WEhaPJKeP3++Xr16Jbw/GjRooHVBUiRFUiQtBkmfffbZq9gQAQEBtWrVCgkJ6dmzZ1RU1MjCiOHDh4eHhwcHB6tklX8VtRo7dmy2Z8M3dim2Q2Gtpld87flGJZ0l6c6dO8uUKfO7AFXqFx8ff+zYsSJdySNHjsyaNatx48a/6/MLDAy8llP5CMJ2QFIkvZpo1KhRwbfOww8/XLg3axYkNmzY8NBDDxW8kk2aNEEQJEVSJPWcpKtXry7gdilbtuy0adMuXbpULCus5cbExBT83v+NGzciCJIiKZJ6SNICJqTly5f3pE2/FStXrgwKCnJaWoqkSIqkfi3prl27CnjkcfPmzQ5Z81WrVpUqVaog1fbY0VIkRVIk9WtJo6KiCrJFZs+e7aiVf/nllwtS7dGjRyMIkiIpkha5pNWqVct3czRr1syB61+/fv18a169enUEQVIkRdKilfTgwYP5bouSJUt6+IqiAkZSUlJBrjY9cOAAgiApkiJpEUq6aNGifLdFaGioYzdBSEhIvvWfM2cOgiApkiJpEUoaGRmZ77ZISEhw7CaIj4/Pt/4DBgxAECRFUiQtQknzfZbzddddd+bMmSLd1tfyYZw8ebJs2bJ5zx4cHAxShbUWToCyeHc5j20orwgnfCc5QtLq1avnvRp169Yt9j0m7wqohnnPXqNGDSRFUiRF0iKU9MYbb8x7NcLCwhwuaZcuXfKevVKlSkiKpEiKpEUoaenSpfNejcjISIdLGhERkffsgYGBSIqkSIqkRShpvqsxbNgwh0uqGnIyB0mRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmR1Bcs9grmsr0hvOLTdMJa+Mb+gKRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqRIiqQ+IakH9hgn7HNesQjfqKRXEOMVX72e2B+QFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmRFEmR1NslLVu2bN61HDBggMO3VERERN6zBwYGIimSIimSFqGkFStWzLuWYWFhDt9SnTt3znv2SpUqISmSIimSFqGk1atXz7uWDz74oMO31AMPPJD37DVr1kRSJEVSJC1CSZs0aZJ3LcuUKXPixAnHbqmTJ0/me4CiadOmSIqkSIqkRShpvgcZFfHx8dlOjfnz5+db/4EDB3rFPucnUHrFIpzwaXoFUj6SYVx7EbNnz863oiEhIY6VtHXr1vnWPy4uDkmRFEmRtAgl3b9/f74VDQgISEpKciCjW7duVd3yrX9qaiqSIimSImkRSqqoVq1avnWtX7++AyWtV69evjUvlNNNSIqkSIqk+cSoUaMKUt3Y2FhHMTplypSCVPuZZ55BUiRFUiQtckn37t1bkOqWLFkyISHBIYyuXr26VKlSBal2cnIykiIpkiJpkUuaXYBroSyCgoLef//9Yt+BBHr58uULUuHg4GDn7LVIiqRI6uOSbtiwoYCVVmY6YcKES5cuFcuuo+VGR0erDgWs7ebNm5EUSZEUST0kqaJx48YFr3rdunXXrVvn4f1m7dq1+d7O5B6FckE+kiIpkiLp74jk5OR8bxbKFQ0aNJg5c2Z6enqR7i4qf8aMGfXr1/9ddQsMDNy7dy+SIimSIqlHJVU8//zzV7EaAQEBtWvXbteuXZ8+faKiokYWRqgclaYya9WqVZArRi+P6Ohop+21SIqkSOoXkl68ePGRRx4p4f3RsGHDCxcuOA0pr9itvaJ5e0V4hYN8sxaJpIqMjIzKlSt79R5ctWrVzMxMB+4xSIqkSOovkir27dt3yy23eOnuW7Fixa+//tqZewySIimS+pGkiqSkpHyfAO3AqFSp0rZt2xy7xyApkiKpf0mqSElJqVmzphftuH/+85/37Nnj5D0GSZEUSf1OUsXRo0ebNWvmFXtt8+bNs7KyHL7HICmSIqk/Smpn8ydPnnzDDTc4dn8tV67clClTPHDPFZIiKZIi6TXFwYMH27Vr57Q9NSAgoEOHDmlpaZ7ZCEiKpEiKpIUQ27dvDw0NLfg970UXqkPHjh137NjhydVHUiRFUiQttEhPT580adK99957dbceXWMSet99902ePPnw4cOeX3EkRVIkRdLCj6ysrKVLl44YMaJt27a1a9e+7bbbgoKCCnEnUGkqUyWrfC1Fyyrqc0pIiqRIiqQEQRAEkhIEQSApQRAEkhIEQSApQRAEgaQEQRBIShAEgaQEQRBIShAEQSApQRAEkhIEQSApQRAEkhIEQRBIShAEgaQEQRBIShAEgaQEQRAEkhIEQSApQRAEkhIEQSApQRAEgaQEQRCei/8Ff2ZvLPFN+iQAAAAASUVORK5CYII="
                                border="0"/></div>
                    </td>
                    <td class="column3 nopad"></td>
                    <td class="column3 nopad"></td>
                    <td class="column6 style0 s total" colspan="2" style="padding-right: 5px">{{__t('Всього')}}:</td>
                    <td class="column7 style2 s total"><span class="subtotal">{{$order->getAllCost()}}</span>&nbsp;{{ setting('currency') }}</td>
                </tr>
                <tr class="row22">
                    <td class="column0 nopad">&nbsp;</td>
                    <td class="column1 nopad">&nbsp;</td>
                    <td class="column2 nopad">&nbsp;</td>
                    <td class="column3 nopad">&nbsp;</td>
                    <td class="column3 nopad">&nbsp;</td>
                    <td class="column6 style0 s total" colspan="2" style="padding-right: 5px">{{__t('Общая скидка')}}:</td>
                    <td class="column7 style0 s total">{{--{{$order->getAllCost() - $order->getTotalCost()}}--}}{{$order->getSaleSum()}}&nbsp;{{ setting('currency') }}</td>
                </tr>
                <tr class="row23">
                    <td class="column0 nopad">&nbsp;</td>
                    <td class="column1 nopad">&nbsp;</td>
                    <td class="column2 nopad">&nbsp;</td>
                    <td class="column3 nopad">&nbsp;</td>
                    <td class="column3 nopad">&nbsp;</td>
                    <td class="column6 style0 s total" colspan="2" style="padding-right: 5px">{!! __t('Вартість доставки')!!}:</td>
                    <td class="column7 style0 s total">@if(!$order->is_delivery_paid_separately){{$order->price_delivery}}@else
                            0 @endif&nbsp;{{ setting('currency') }}</td>
                </tr>
                <tr class="row25">
                    <td class="column0 nopad">&nbsp;</td>
                    <td class="column1 nopad">&nbsp;</td>
                    <td class="column2 nopad">&nbsp;</td>
                    <td class="column3 nopad">&nbsp;</td>
                    <td class="column3 nopad">&nbsp;</td>
                    <td class="column6 style4 s big total" colspan="2" style="padding-top: 15px;padding-right: 5px">{{__t('К оплате')}}:</td>
                    <td class="column7 style4 s big total"
                        style="padding-top: 15px"><span class="cost">{{$order->getPriceForDocumentsAttribute()}}</span>&nbsp;{{ setting('currency') }}</td>
                </tr>
                <tr class="row26">
                    <td class="column0">&nbsp;</td>
                    <td class="column1">&nbsp;</td>
                    <td class="column2">&nbsp;</td>
                    <td class="column3">&nbsp;</td>
                    <td class="column4">&nbsp;</td>
                    <td class="column5">&nbsp;</td>
                    <td class="column6">&nbsp;</td>
                    <td class="column7">&nbsp;</td>
                </tr>
                <tr class="row27">
                    <td class="column0">&nbsp;</td>
                    <td class="column1">&nbsp;</td>
                    <td class="column2">&nbsp;</td>
                    <td class="column3">&nbsp;</td>
                    <td class="column4">&nbsp;</td>
                    <td class="column5">&nbsp;</td>
                    <td class="column6">&nbsp;</td>
                    <td class="column7">&nbsp;</td>
                </tr>
                <tr class="row28">
                    <td class="column0">&nbsp;</td>
                    <td class="column1">&nbsp;</td>
                    <td class="column2">&nbsp;</td>
                    <td class="column3">&nbsp;</td>
                    <td class="column4">&nbsp;</td>
                    <td class="column5">&nbsp;</td>
                    <td class="column6">&nbsp;</td>
                    <td class="column7">&nbsp;</td>
                </tr>
                <tr class="row29">
                    <td class="column0">&nbsp;</td>
                    <td class="column1">&nbsp;</td>
                    <td class="column2">&nbsp;</td>
                    <td class="column3">&nbsp;</td>
                    <td class="column4">&nbsp;</td>
                    <td class="column5">&nbsp;</td>
                    <td class="column6">&nbsp;</td>
                    <td class="column7">&nbsp;</td>
                </tr>
                <tr class="row30">
                    <td class="column2 style9 s style9 e" colspan="8">{{__t('Спасибо за выбор нашего магазина.')}}</td>
                </tr>
                <tr class="row31">
                    <td class="column2 style9 s style9 e" colspan="8">{{__t('Будем благодарны, если оставите отзыв о нашей работе по ссылке QR')}}
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
