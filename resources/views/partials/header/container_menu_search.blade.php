<div class="container">
    <div class="header-bottom-row flex v--center pt-8 pb-8">

        <div class="get-catalog-button-wrapper" id="get-catalog-button">
            <button class="main-btn blue-big icon-left get-catalog">
                <span class="icon">
                    <img src="/assets/images/catalog.svg" alt="{{__t('Каталог')}}">
                </span>
                {{__t('Каталог')}}
            </button>
            <livewire:catalogmenu.header-desktop lazy />
        </div>

        <a href="{!! setting('ssylka-na-katalog-akcii') !!}" class="sale flex v--center color--red fsz-18 fw-500"><img src="/assets/images/sale.svg" alt="">{{__t('Акції')}}</a>


        @livewire('search.live-form')


        @include('partials.tel_info')
        @include('partials.header.auth_icon')
        <div class="buttons-block flex v--center ml-auto">
            <livewire:compare.count lazy />
            <livewire:favorite.count lazy />
            <livewire:cart.count lazy />
        </div>
    </div>
</div>
