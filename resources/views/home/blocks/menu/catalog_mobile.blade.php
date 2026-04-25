{{-- Мобильное меню каталога на главной сайта --}}
<div class="mobile-block">
    <button class="main-btn blue-big icon-left get-mobile-catalog">
        <span class="icon">
            <img src="assets/images/catalog.svg" alt="{{__t('Каталог товарів')}}">
        </span>{{__t('Каталог товарів')}}
    </button>
    <livewire:catalogmenu.home-mobile />
</div>