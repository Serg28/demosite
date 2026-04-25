{{-- Первый экран главной страницы: меню каталога (моб. и десктоп), баннер-слайдер --}}
<div class="title pt-16">
    <div class="container hover-wrap" id="hover-container">
        <div class="title__wrap flex v--stretch h--between">
            {{-- Десктопное меню каталога --}}

            @include('home.blocks.menu.catalog_desktop')
            {{-- / --}}
            {{-- Баннер-слайдер --}}
            @include('home.blocks.first_screen.slider')
            {{-- / --}}
        </div>
    </div>
    <div class="container">
        {{-- Избранные категории --}}
        @include('home.blocks.first_screen.favorite_categories')
        {{-- / --}}
        {{-- Мобильное меню каталога --}}
        @include('home.blocks.menu.catalog_mobile')
        {{-- / --}}
        {{-- Избранные бренды --}}
        @include('home.blocks.first_screen.favorite_brands')
        {{-- / --}}
    </div>
</div>
