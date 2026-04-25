<div class="block_accordion filters_category active">
    <div class="title_accordion">
        <h3>{{$title}}</h3>
    </div>

    <div class="main_accordion active">
        <div class="range_container">

            {{-- Переменные:
             Интервал - минимальное возможное значение: {{$results['price']['min'] ?? 1}}
             Интервал - максимальное возможное значение: {{$results['price']['max']}}
             Фильтрация - минимальное текущее значение: {{$filter->minPrice() ?: $results['price']['min']}}
             Фильтрация - минимальное текущее значение: {{$filter->maxPrice() ?: $results['price']['max']}}

             URL всего фильтра без цены: {{$filter->withoutPrice()}}

             --}}


            <div class="slider-wrapper">
                <div id="sliderPrices" class="slider-container select-price-row"
                     data-url="{{$filter->urlWithoutParam('price')}}"
                     data-min="{{$results['price']['min'] ?: 1}}"
                     data-max="{{$results['price']['max']}}"
                     data-min-value="{{$filter->minPrice() ?: $results['price']['min']}}"
                     data-max-value="{{$filter->maxPrice() ?: $results['price']['max']}}"
                >
                    <div class="values form_control">
                        <div class="wrap_form_control">
                            <input type="number" class="form_control_container__time__input minValue" value="{{$filter->minPrice() ?: $results['price']['min']}}">
                            <span>&#8211;</span>
                            <input type="number" class="form_control_container__time__input maxValue" value="{{$filter->maxPrice() ?: $results['price']['max']}}">
                        </div>
                        <a class="ok_price" href="#">Ok</a>
                    </div>

                    <div class="range-track">
                        <div class="range"></div>
                    </div>
                    <input type="range" class="minSlider" min="{{$results['price']['min'] ?: 1}}" max="{{$results['price']['max']}}" value="{{$filter->minPrice() ?: $results['price']['min']}}">
                    <input type="range" class="maxSlider" min="{{$results['price']['min'] ?: 1}}" max="{{$results['price']['max']}}" value="{{$filter->maxPrice() ?: $results['price']['max']}}">
                </div>
            </div>
        </div>
    </div>

</div>