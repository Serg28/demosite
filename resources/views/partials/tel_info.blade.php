@php
    $phone = explode(',', setting('telinfophone'));
@endphp
<div class="tel-info @if(!empty($mobil)) mobile @endif">
    <div class="tel fsz-16 fw-600 color--black flex v--center relative">
       @if(empty($mobil))
           @if(!empty($check))<div class="visible flex-v--center">@endif
            @if(!empty($phone[0])){{$phone[0]}}@endif
                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="6" viewBox="0 0 8 6" fill="none">
                    <path d="M1 1L4 4L7 1" stroke="#0A0527" stroke-width="2"/>
                </svg>
            @if(!empty($check))</div> @endif
        @endif
        <div class="tel-sub-menu absolute flex fd--column">
            <div class="tels">
                @foreach($phone as $tel)
                <a href="tel:{{$tel}}" class="tel color--black fw-600 fsz-16">{{$tel}}</a>
                @endforeach
            </div>
            <p class="fsz-13">{!! setting('grafik-raboty-v-futere') !!}</p>
            <div class="socs-row">
                <span class="fsz-12 color--gray-100">{{ __t('Ми у месенджерах') }}</span>
                <div class="socs-wrap flex v--center">
                    @if(setting('link-to-telegram'))
                        <a href="{{ setting('link-to-telegram') }}" class="socs" target="_blank">
                            <img src="/assets/images/telegram.svg" alt="">
                        </a>
                    @endif
                    @if(setting('link-to-viber'))
                        <a href="{{ setting('link-to-viber') }}" class="socs" target="_blank">
                            <img src="/assets/images/viber.svg" alt="">
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if(empty($mobil)) <div class="info fsz-13 color--black-100">{!! setting('grafik-raboty-v-futere') !!}</div> @endif
</div>
