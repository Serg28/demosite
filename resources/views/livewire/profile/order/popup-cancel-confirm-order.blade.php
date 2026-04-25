<div>
    @if($open)
    <div class="main-popup-wrap mfp-wrap mfp-close-btn-in mfp-auto-cursor mfp-zoom-in mfp-ready" tabindex="-1"
         style="overflow: hidden auto;display:block">
        <div class="mfp-container mfp-s-ready mfp-inline-holder">
            <div class="mfp-content">
                <div class="white-popup">

                    <div class="mfp-title d-flex">
                        <p>{{__t('Увага!')}}</p>
                    </div>

                    <div class="content-wrap">
                        <div class="text">
                            <p>{!! str_replace('[ordernum]','<strong>'.$order->order_number.'</strong>',__t('Ви дійсно бажаєте скасувати замовлення [ordernum] ?')) !!}</p>
                        </div>
                        <div class="form-group d-flex">
                            <button class="main-btn bg-gray-200 mw-a w-a"  wire:click="$set('open', false)">
                                {{__t('Ні')}}
                            </button>
                            <button class="main-btn bg-green mw-a w-a" wire:click="cancelation">
                                <span wire:loading.class="spinner pa m0" wire:target="cancelation" ></span>
                                <span wire:loading.class="opacity-0" wire:target="cancelation">{{__t('Так, скасувати')}}</span>
                            </button>
                        </div>
                    </div>

                    <button title="Close (Esc)" type="button" class="mfp-close" wire:click="$set('open', false)">
                        <img src="/img/pc.svg" alt="{{__t('Закрити')}}">
                    </button>

                </div>
            </div>
            <div class="mfp-preloader">Loading...</div>
        </div>
    </div
    >@endif
</div>
