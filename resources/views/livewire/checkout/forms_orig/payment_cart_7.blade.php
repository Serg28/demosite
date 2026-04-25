<div class="bottom-price-row">
    <p style="font-weight: 700"><span>{{__t('Всього')}}</span></p>
    <span style="font-weight: 700">@money($payment['total']) <strong>{{ setting('currency') }}</strong></span>
</div>
<div class="bottom-price-row">
    <p><span><small>{{__t('Первый взнос')}}</small></span></p>
    <span>@money($payment['total']/$payment['cur_partscount']) <strong>{{ setting('currency') }}</strong></span>
</div>
<div class="bottom-price-row">
    <p><span><small>{{__t('Срок')}}</small></span></p>
    <span>{{($payment['cur_partscount']-1)}} <strong>{{__t('мес.')}}</strong></span>
</div>
<div class="bottom-price-row">
    <p><span><small>{{__t('Ежемесячный платеж')}}</small></span></p>
    <span>@money($payment['total']/$payment['cur_partscount']) <strong>{{ setting('currency') }}</strong></span>
</div>
<div class="bottom-price-row">
    <p><span>{{ __t('Всього до оплати') }}</span></p>
    <span>@money($payment['total']/$payment['cur_partscount']) <strong>{{ setting('currency') }}</strong></span>
</div>
