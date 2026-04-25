<div>
    @if($open)
        <div class="main-popup-wrap mfp-wrap mfp-close-btn-in mfp-auto-cursor mfp-zoom-in mfp-ready" tabindex="-1"
             style="overflow: hidden auto;display:block">
            <div class="mfp-container mfp-s-ready mfp-inline-holder">
                <div class="mfp-content">
                    <div class="white-popup">

                        <div class="mfp-title d-flex">
                            <p>{!! $title !!}</p>
                        </div>

                        <div class="content-wrap">
                            <div class="text">
                                <p>{!! $message !!}</p>
                            </div>
                            <div class="form-group d-flex">
                                <a href="{{$referrer}}" class="main-btn bg-green  mw-a w-a"  wire:click="$set('open', false)" >
                                    {{__t('Закрити')}}
                                </a>
                            </div>
                        </div>

                        <a href="{{$referrer}}" title="Close (Esc)" type="button" class="mfp-close" wire:click="$set('open', false)">
                            <img src="/img/pc.svg" alt="{{__t('Закрити')}}">
                        </a>

                    </div>
                </div>
                <div class="mfp-preloader">Loading...</div>
            </div>
        </div
        >@endif
</div>
