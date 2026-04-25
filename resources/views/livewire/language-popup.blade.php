<div>
    <span><!-- empty --></span>
    @if($showPopup)

        <div class="main-popup-wrap mfp-wrap mfp-close-btn-in mfp-auto-cursor mfp-zoom-in mfp-ready" tabindex="-1" style="overflow: hidden auto;display:block">
            <div class="mfp-container mfp-s-ready mfp-inline-holder"><div class="mfp-content">
                    <div class="white-popup">
                        @php
                            $url = isset($page) ? $page->getFullUrl('ua') : \Request::fullUrl();
                        @endphp
                        <div class="mfp-title d-flex">
                            <p>{{__t('Внимание!')}}</p>
                        </div>

                        <div class="content-wrap">
                            <div class="text">
                                <p>{{__t('Це російська версія сайту. Бажаєте перейти на українську?')}}</p>
                            </div>
                            <div class="form-group">
                                <a href="{{geturl($url,'ua')}}" class="main-btn bg-green"
                                   wire:click="setUrl()">{{__t('Так, перейти на українську')}}</a>
                                <button wire:click="setUrl()" class="main-btn bg-gray-200">{{__t('Ні')}}</button>
                            </div>
                        </div>

                        <button title="Close (Esc)" type="button" class="mfp-close" wire:click="setUrl()">
                            <img src="/img/pc.svg" alt="{{__t('Закрити')}}">
                        </button>

                    </div>
                </div>
                <div class="mfp-preloader">Loading...</div>
            </div>
        </div>

        <link rel="stylesheet" type="text/css" href="/css/modals.css">
    @endif
</div>
