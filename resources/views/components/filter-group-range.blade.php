{{-- Переменные:
             Интервал - минимальное возможное значение: {{$results['price']['min'] ?? 1}}
             Интервал - максимальное возможное значение: {{$results['price']['max']}}
             Фильтрация - минимальное текущее значение: {{$filter->minPrice() ?: $results['price']['min']}}
             Фильтрация - минимальное текущее значение: {{$filter->maxPrice() ?: $results['price']['max']}}

             URL всего фильтра без цены: {{$filter->withoutPrice()}}

             --}}
<div class="cat-cell filters_category active">
    <p class="fsz-16 fw-600 color--black pl-12">{{$title}}</p>
    <div id="sliderPrices" class="as-range-wrap"
         data-url="{{$filter->urlWithoutParam('price')}}"
         data-min="{{$results['price']['min'] ?: 1}}"
         data-max="{{$results['price']['max']}}"
         data-min-value="{{$filter->minPrice() ?: $results['price']['min']}}"
         data-max-value="{{$filter->maxPrice() ?: $results['price']['max']}}"
    >
        <div class="range_container">
            <div class="form_control">
                <div class="form_control_container">
                    <input class="form_control_container__time__input minValue" type="text" value="{{$filter->minPrice() ?: $results['price']['min']}}" />
                </div>
                <span class="devider">-</span>
                <div class="form_control_container">
                    <input class="form_control_container__time__input maxValue" type="text" value="{{$filter->maxPrice() ?: $results['price']['max']}}"/>
                </div>
                <div class="main-btn blue-small">ОК</div>
            </div>
            <div class="sliders_control">
                <div class="range-track">
                    <div class="range"></div>
                </div>
                <input type="range"  class="minSlider" min="{{$results['price']['min'] ?: 1}}" max="{{$results['price']['max']}}" value="{{$filter->minPrice() ?: $results['price']['min']}}" />
                <input type="range" class="maxSlider" min="{{$results['price']['min'] ?: 1}}" max="{{$results['price']['max']}}" value="{{$filter->maxPrice() ?: $results['price']['max']}}"/>
            </div>
        </div>
    </div>
</div>