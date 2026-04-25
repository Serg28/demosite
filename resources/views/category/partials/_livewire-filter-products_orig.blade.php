{{-- Здесь размещаем весь код, который обрабатывается livewire-компонентом app/Livewire/Category/FilterProducts.php
 При работе с фильтром весь этот шаблон будет перезагружаться и отображать информацию из каталога с помощью вышеупомянутого компонента

По-умолчанию из компонента доступны такие переменные:

filter - данные о сортировке
results - данные с результатами (список товаров, данные о фильтрах)
count - количество найденных товаров
filter_cont - html блок со сформированным фильтром

Далее эти переменные можно использоват как угодно. Напр., они будут доступны во вложенных шаблонах
 --}}

{{-- SEO-секция, чтобы отображать актуальные данные. Поскольку осуществляем поиск не через контроллер, данные о фильтрах будут доступны только здесь
 а не из главного шаблона. Заодно при изменении фильтра изменения сразу будут отображаться --}}
@section('seo_tags')
    @include('partials.seo_for_catalog')
@stop
{{--  --}}

<div class="lw-catalog-filter">
    {{-- Категориии --}}
    @if($categoriesFiltered)
    <div class="main_screen-catalog category3">
        <div class="container">
            <h1 class="title-page">{{$this->page->getSeoH1()}}</h1>
            <div class="grid_cat-catalog level level3 disable-scrollbar">
                <div class="scrl disable-scrollbar">
                    <div class="scrl-row disable-scrollbar" >
                        @foreach($categoriesFiltered as $category)
                            <a @if($category->id !== $this->page->id) href="{{$category->getUrl()}}{{$filter->getSelectedBrandUrlFilter() ?? ''}}" wire:navigate.hover @endif
                            class="@if($category->id === $this->page->id) disabled @endif cat-l3" wire:key="cat-filtered-{{$category->id}}" wire:loading.class="opacity-50">
                                <picture>
                                    <source media="(min-width: 1200px)" srcset="{!! $category->getImgPath('', 160) !!}">
                                    <source media="(min-width: 768px)" srcset="{!! $category->getImgPath('', 160) !!}">
                                    <img loading="lazy" src="{!! $category->getImgPath('', 160) !!}" alt="{{e($category->t('title'))}}" width="160" height="93">
                                </picture>
                                <span class="text">{{$category->t('title')}}{{--{{$category->document_count}}--}}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endif
    {{-- /Категории --}}

    {{-- Основная часть --}}
    <div x-data="{ mobileOpen:'{{$mobileFilterActive ?? false}}' }" class="catalog_filter" >
        <div class="container">
            <div class="top-catalog_filter">
                {{-- Верхняя панель --}}

                {{-- Слева - сброс и статистика --}}
                <div class="left_top-catalog_filter">
                    <p>{{__t('Обрано')}} {{$count}}  {{trans_choice(__t('{0}товаров|[1]товар|[2,4]товара|[5,*]товаров'),$count)}}</p>

                    @if($filter->issetFilters())
                    <button @click="location.href='{{$this->page->getUrl()}}'">
                        <img src="/img/cross.svg" alt="{{__t('Скинути все')}}" width="18" height="18"> {{__t('Скинути все')}}
                    </button>
                    @endif

                    <button class="mobile_btn_filter main-btn">
                        <img src="/img/icon_filter.svg" alt="filter">
                        {{__t('Фільтри')}}
                        <span class="num">{{$count}}</span>
                    </button>
                </div>
                {{-- /Слева - сброс и статистика --}}

                {{-- Справа - сортировка --}}
                <div class="right_top-catalog_filter">
                    <div class="select_wrap">
                        <div class="select sort_by">
                            <div class="visible-row">
                                <span class="titile_select">За рейтингом</span>
                                <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none">
									<path d="M11 1.63672L6.70711 5.92961C6.31658 6.32014 5.68342 6.32014 5.29289 5.92961L1 1.63672" stroke="#5F276D" stroke-width="1.5" stroke-linecap="round"/>
								  </svg></span>
                            </div>
                            <div class="droppdown disable-scrollbar">
                                <div class="droppdown-row">За рейтингом</div>
                                <div class="droppdown-row">Топ</div>
                                <div class="droppdown-row">Новинки</div>
                                <div class="droppdown-row">Розпродаж</div>
                                <div class="droppdown-row">Від дешевих до дорогих</div>
                                <div class="droppdown-row">Від дорогих до дешевих</div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- /Справа - сортировка --}}

                {{-- / --}}
            </div>





            <div class="main-catalog_filter">
                {{----------------- Блок фильтра ----------------}}
                @include('category.partials.filter')
                {{---------------- /Блок фильтра ----------------}}


                {{------------- Блок вывода товаров -------------}}
                @include('category.partials.products_container')
                {{------------ /Блок вывода товаров -------------}}

            </div>


            {{-- Пагинация --}}
            @include('partials.paginate', ['items' => $results['products']])
            {{-- /Пагинация --}}
        </div>
    </div>
    {{--  --}}

</div>

@push('footer-scripts')
     {{--<link rel="stylesheet" type="text/css" href="{{mix('/css/rangeslider.min.css')}}"  data-navigate-track> --}}
    <script src="{{mix('/js/catalog.min.js')}}" data-navigate-track></script>
@endpush
