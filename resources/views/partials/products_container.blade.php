<div class="catalog-level-2 flex v--stretch h--wrap" wire:loading.class="opacity-50">
    @if($products)
        @loop($products as $product)
        @include('partials.product')
        @endloop
    @else
        <p>{{__t('Товаров по текущему запросу нету')	}}</p>
    @endif
</div>
{{-- Пагинация --}}
@include('partials.paginate', ['items' => $results['products']])
{{-- /Пагинация --}}