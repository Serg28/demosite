@if($products && $count>0)
    <div class="product_bl" wire:loading.class="opacity-50">
        <div class="card-wrap">
            @loop($products as $product)
            @include('partials.product')
            @endloop
        </div>
    </div>

@else
    <div class="product_bl" wire:loading.class="opacity-50">
        <p>{{__t('К сожалению, по запросу ничего не найдено')	}}</p>
    </div>
@endif