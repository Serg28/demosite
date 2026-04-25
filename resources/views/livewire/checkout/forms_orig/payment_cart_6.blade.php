<div class="bottom-price-row">
    <p style="font-weight: 700"><span>{{__t('Всього')}}</span></p>
    <span style="font-weight: 700">@money($payment['total']) <strong>{{ setting('currency') }}</strong></span>
</div>
<div class="bottom-price-row">
    <p><span><small>{{__t('Первый взнос')}}</small></span></p>
    <span><span class="privatpayparts_permonth"></span> <strong>{{ setting('currency') }}</strong></span>
</div>
<div class="bottom-price-row">
    <p><span><small>{{__t('Срок')}}</small></span></p>
    <span>{{($payment['cur_partscount']-1)}} <strong>{{__t('мес.')}}</strong></span>
</div>
<div class="bottom-price-row">
    <p><span><small>{{__t('Ежемесячный платеж')}}</small></span></p>
    <span><span class="privatpayparts_permonth"></span> <strong>{{ setting('currency') }}</strong></span>
</div>
<div class="bottom-price-row">
    <p><span><small>{{__t('Общая стоимость кредита')}}</small></span></p>
    <span><span class="privatpayparts_allSum"></span> <strong>{{ setting('currency') }}</strong></span>
</div>
<div class="bottom-price-row">
    <p><span>{{ __t('Всього до оплати') }}</span></p>
    <span><span class="privatpayparts_permonth"></span> <strong>{{ setting('currency') }}</strong></span>
</div>

<script>
    var privatpayparts_type = '{{config('services.privat_pay_parts.type')}}';
    privatpayparts_type = (privatpayparts_type === 'II' || privatpayparts_type === 'IA') ? 'ia' : 'pp';
    const script = document.createElement('script');
    script.type = "text/javascript";
    script.src = 'https://ppcalc.privatbank.ua/pp_calculator/resources/js/calculator.js';
    // Слушаем, когда загрузится внешний скрипт и запускаем функцию
    script.addEventListener('load', () => {
        scriptInit(privatpayparts_type);
    });
    document.body.append(script);

    // Инициализация функции
    function scriptInit(type) {
        var resCalc = PP_CALCULATOR.calculatePhys({{$payment['cur_partscount']}}, {{$payment['total']}});
        var allSumm = (type === 'pp') ? resCalc.ppAll : resCalc.ipAll;
        var perMonth = (type === 'pp') ? resCalc.ppValue : resCalc.ipValue;
        $('.privatpayparts_permonth').html(perMonth);
        $('.privatpayparts_allSum').html(parseFloat(allSumm.replace(/[^\d.]/ig, '')));
    }
</script>
