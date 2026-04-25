    {!! $filter_block !!}
    <div class="sale-block">
        @if($count)
            @include('promotion.partials.products_container')
        @else
            <div class="catalog-container info-center">
                <p>{{__t('Товаров в данной категории пока нет')	}}</p>
            </div>
        @endif
    </div>



