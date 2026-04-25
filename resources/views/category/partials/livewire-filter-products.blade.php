{{-- Здесь размещаем весь код, который обрабатывается livewire-компонентом app/Livewire/Category/FilterProducts.php
 При работе с фильтром весь этот шаблон будет перезагружаться и отображать информацию из каталога с помощью вышеупомянутого компонента

По-умолчанию из компонента доступны такие переменные:

filter - данные о сортировке
results - данные с результатами (список товаров, данные о фильтрах)
count - количество найденных товаров
categoriesFiltered - непустые категории
products - найденные товары с пагинацией

Далее эти переменные можно использоват как угодно. Напр., они будут доступны во вложенных шаблонах
 --}}


{{--
 Пример с выбранными фильтрами

 @if($filter->issetFilters())
		<div class="widget filters_category">
			<h5 class="widget_title">{{__t('Ви обрали')}}</h5>
			<div class="filter-choosed-item-container">
				@foreach($filter->getSelectedFilters() as $category => $filters)
				    <!-- Далее код файла @include('category.partials.select_filter') -->
					@foreach($filters as $option)
                    <div class="chip choosed-item choosed">
                        <p>{{$option->t('title')}}</p><a href="{{$filter->urlFilter($option)}}"><i class="close ti-close"></i></a>
                    </div>
                    @endforeach
                    <!-- / -->
				@endforeach
				@if ($filter->minPrice() || $filter->maxPrice())
					<div class="chip choosed-item choosed">
						<p>{{__t('Вартість,')}} {{ setting('currency') }}:  {{$filter->minPrice()}} - {{$filter->maxPrice()}}</p>
						<a href="{{$filter->withoutPrice()}}"><i class="close ti-close"></i></a>
					</div>
				@endif
				<a href="{{$page->getUrl()}}" class="btn btn-line-fill btn-sm" >{{__t('Очистити')}}</a>
			</div>
		</div>
	@endif

 --}}



{{-- SEO-секция, чтобы отображать актуальные данные. Поскольку осуществляем поиск не через контроллер, данные о фильтрах будут доступны только здесь
 а не из главного шаблона. Заодно при изменении фильтра изменения сразу будут отображаться --}}
@section('seo_tags')
    @include('partials.seo_for_catalog')
@stop
{{--  --}}

<div class="catalog lw-catalog-filter">
    <div class="container">
        <h1 class="fsz-34 fw-600 catalog-heading">{{$this->page->getSeoH1()}}</h1>
        <div class="flex-row flex v--center h--between mt-24 title-catalog-row">
            <div class="brands flex v--center">
                <a href="" class="brand flex v--center h--center"><img src="/assets/images/apple-big.svg" alt=""></a>
                <a href="" class="brand flex v--center h--center"><img src="/assets/images/samsung.svg" alt=""></a>
                <a href="" class="brand flex v--center h--center"><img src="/assets/images/xiaomi.svg" alt=""></a>
                <a href="" class="brand flex v--center h--center"><img src="/assets/images/huawei.svg" alt=""></a>
                <a href="" class="brand flex v--center h--center"><img src="/assets/images/oppo.svg" alt=""></a>
                <a href="" class="brand flex v--center h--center"><img src="/assets/images/realme.svg" alt=""></a>
                <a href="" class="brand flex v--center h--center"><img src="/assets/images/moto.svg" alt=""></a>
                <a href="" class="brand flex v--center h--center"><img src="/assets/images/nokia.svg" alt=""></a>
            </div>
            @include('partials.sorting')
        </div>
        <div class="hidden-filter-buttons">
            <div class="btn main-btn blue-small icon-left get-filter">
                <div class="icon"><img src="/assets/images/filter.svg" alt="{{__t('Фільтри')}}"></div>
                {{__t('Фільтри')}}
            </div>
            @include('partials.sorting_mobile')
        </div>
        <div class="catalog__wrap">


            {{-- Левый сайдбар с фильтрами --}}
            <div class="catalog-side-bar">
                <div class="mobile-filter-top-row">
                    <div class="fixed-row">
                        <p class="fsz-18 fw-600">{{__t('Фільтри')}}</p>
                        <div class="filter-closer"></div>
                    </div>
                </div>

                <div class="buttons-row-mob">
                    @if($filter->issetFilters())<a href="{{$page->getUrl()}}" wire:navigate class="main-btn border-small cancel-filter">{{__t('Скинути')}}</a>@endif
                    <button class="main-btn blue-small">{{__t('Застосувати')}}<span class="num"></span></button>
                </div>

                <div class="catalog-cell-wrapper">
                    <div class="labels-row">
                        <p class="fsz-16 color--gray">{{__t('Ви обрали')}} {{$count}}  {{trans_choice(__t('{0}товаров|[1]товар|[2,4]товара|[5,*]товаров'),$count)}}</p>
                        <div class="labels flex v--center h--wrap">
                            @if($filter->issetFilters())
                                @loop($filter->getSelectedFilters() as $category => $filters)
                                    @include('category.partials.select_filter')
                                @endloop
                                @if ($filter->minPrice() || $filter->maxPrice())
                                    <div class="label flex v--center">{{__t('Вартість,')}} {{ setting('currency') }}:  {{$filter->minPrice()}} - {{$filter->maxPrice()}} <a href="{{$filter->withoutPrice()}}" class="icon"><img src="/assets/images/close.svg" alt=""></a></div>
                                @endif
                            @endif
                        </div>
                    </div>


                    {{-- Фильтры --}}

                    <x-filter-group-range :results="$results" :filter="$filter" :title="__t('Ціна')" />
                    @loop($characteristics as $index => $characteristic)
                        <x-filter-group-checkbox :characteristic="$characteristic" :results="$results" :filter="$filter" :selectedFilters="$selectedFilters" />
                    @endloop

                    {{-- Категории (для Акций и т.д.) --}}
                    @if($page->id == setting('id-kategorii-akcii') && !empty($categoriesFiltered))
                        <div class="cat-cell">
                            <p class="fsz-16 fw-600 color--black pl-12">{{__t('Категории')}}</p>
                            <ul class="flex fd--column pl-12">
                                @loop($categoriesFiltered as $category)
                                    @if($category->id!==$page->id)
                                        <li><a href="{{$filter->urlWithoutParam('category')}}/category={{$category->id}}">{{$category->t('title')}} {{--<span class="fsz-12">({{$category->document_count}})</span>--}}</a> </li>
                                    @endif
                                @endloop
                            </ul>
                        </div>
                    @endif
                    {{-- Категории (для Акций и т.д.) --}}

                    {{-- /Фильтры --}}

                </div>
            </div>
            {{-- /Левый сайдбар с фильтрами --}}

            {{-- Правая часть - товары --}}
            <div class="product-screen catalog-content mb-0">
                <div class="filter-row flex v--center h--wrap">
                    <p class="fsz-16 color--gray">{{__t('Ви обрали')}} {{$count}}  {{trans_choice(__t('{0}товаров|[1]товар|[2,4]товара|[5,*]товаров'),$count)}}</p>
                    @if($filter->issetFilters())
                        @loop($filter->getSelectedFilters() as $category => $filters)
                            @include('category.partials.select_filter')
                        @endloop
                        @if ($filter->minPrice() || $filter->maxPrice())
                            <div class="label flex v--center">{{__t('Вартість,')}} {{ setting('currency') }}:  {{$filter->minPrice()}} - {{$filter->maxPrice()}} <a href="{{$filter->withoutPrice()}}" class="icon"><img src="/assets/images/close.svg" alt=""></a></div>
                        @endif
                        <a href="{{$page->getUrl()}}" class="clear fsz-15 fw-600 color--blue">{{__t('Скинути')}}</a>
                    @endif
                </div>

                {{-- Товары --}}
                @include('partials.products_container')
                {{-- /Товары --}}
            </div>
            {{-- /Правая часть - товары --}}

        </div>
    </div>
</div>

@push('footer-scripts')
     {{--<link rel="stylesheet" type="text/css" href="{{mix('/css/rangeslider.min.css')}}"  data-navigate-track> --}}
    <script src="{{mix('/assets/js/catalog.min.js')}}" data-navigate-track></script>


     {{--
     <script>
         if ('loading' in HTMLImageElement.prototype) {
             const images = document.querySelectorAll('img[loading="lazy"]');
             images.forEach(img => {
                 console.log(img);
                 img.src = img.dataset.src;
             });
         } else {
             // Dynamically import the LazySizes library
             const script = document.createElement('script');
             script.src =
                 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.1.2/lazysizes.min.js';
             document.body.appendChild(script);
         }
     </script>
     --}}
@endpush
