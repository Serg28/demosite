@if($count>0)
    {!! $filter_block !!}
   @include('promotion.partials.promotions_container')
@else
    <div class="catalog-container info-center">
        <p>{{__t('Акций по данному запросу нет')}}</p>
    </div>
@endif
