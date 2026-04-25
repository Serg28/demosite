@php
    $sum = (!empty($price) && !empty($count)) ? (int)round($price/$count) : 0;
    $plural = inflection($count, ['платіж', 'платежі', 'платежів'])
@endphp
@if(!empty($sum))
<div class="swiper-slide">
    <div class="bank flex v--start p-8">
        <div class="icon"><img src="{{$logo}}" alt="#"></div> {{-- /assets/images/mono-black.svg --}}
        <p class="fsz-14 color--gray ml-12">{!! str_replace(['[count]', '[plural]', '[sum]', '[currency]'],[$count, $plural, $sum, setting('currency')], __t('[count] [plural] по <strong>[sum] [currency]</strong>'))!!} </p>
    </div>
</div>
@endif