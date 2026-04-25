<div class="filter_bl {{$mobileFilterActive ? 'active' : ''}}">
    <div class="close_filter_bl">
        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
            <path d="M1.64844 1.5L15.6483 15.5" stroke="#5F276D" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M15.6484 1.5L1.64861 15.5" stroke="#5F276D" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
    </div>
    <div class="wrap_filter_bl">
        <div class="mobile_title">
            {{__t('Фільтри')}}
            {{--
            <button>
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.33203 6.3335L11.6653 11.6668" stroke="#F26238" stroke-width="1.5"
                          stroke-linecap="round" />
                    <path d="M11.668 6.3335L6.3347 11.6668" stroke="#F26238" stroke-width="1.5"
                          stroke-linecap="round" />
                    <circle cx="9" cy="9" r="7.25" stroke="#F26238" stroke-width="1.5" />
                </svg>
                Скинути все
            </button> --}}
            @if($filter->issetFilters())
                <button @click="location.href='{{$page->getUrl()}}'">
                    <img src="/img/cross.svg" alt="{{__t('Скинути все')}}" width="18" height="18"> {{__t('Скинути все')}}
                </button>
            @endif
        </div>
        <div class="block_accordion_wrapper">


            <x-filter-group-range :results="$results" :filter="$filter" :title="__t('Ціна')" />

            @foreach($characteristics as $index => $characteristic)
                <x-filter-group-checkbox :characteristic="$characteristic" :results="$results" :filter="$filter" :selectedFilters="$selectedFilters" />
            @endforeach

        </div>
        <button class="mobile_btn_show_pr main-btn">{{__t('Показати')}} {{$count}}  {{trans_choice(__t('{0}товаров|[1]товар|[2,4]товара|[5,*]товаров'),$count)}}</button>
    </div>
</div>