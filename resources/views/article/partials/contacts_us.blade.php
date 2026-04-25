<div class="{{$class}}">
    @php
        $phone = explode(',', setting('telinfophone'));
    @endphp
    <p class="fsz-18 fw-600">{{__t('Зв\'яжіться з нами')}}</p>
    @loop($phone as $tel)
        <a href="tel:{{$tel}}" class="color--black mt-24">{{$tel}}</a>
    @endloop
    <a href="mailto:{{setting('email-v-futere')}}" class="color--black mt-16">{{setting('email-v-futere')}}</a>
    <span class="mt-24 color--gray">{{__t('Центр обслуговування клієнтів працює')}} {!! setting('grafik-raboty-v-futere') !!}</span>
</div>