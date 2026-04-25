<span>
    @if($product->isActive())
        {{--@if($cartItem->isEmpty()) --}}
            <button class="main-btn blue-small icon-left mt-24 btn-addtocart"
                    type="button"
                    wire:click="addToCart({{ $product->id, 2 }})"
                    wire:loading.attr="disabled"
                    data-count="1"
                    @if(setting('checkbox_dynamic_remarketing_google'))
                        data-dynamic="{{ setting('code_dynamic_remarketing_google') }}"
                    @endif
                    @if(setting('checkbox_google_analytics_four'))
                        data-ga4-addtocart
                    @endif
                    data-sum="{{ $product->getPrice() }}"
                    data-id="{{ $product->id }}"
                    data-options="">
                <span wire:loading.class="spinner" wire:target="addToCart"></span>
                <span class="icon"><img src="/assets/images/cart-white.svg" alt=""></span>{{__t('У кошик')}}
                {{--<span wire:loading.remove wire:target="addToCart">+ {{__t('Додати в кошик')}}</span>
                <span wire:loading wire:target="addToCart"> {{__t('Подождите...')}}</span> --}}
            </button>
        {{--@else
            <button class="main-btn blue-small icon-left mt-24 btn-addtocart" type="button" wire:click="openCart">
                <span wire:loading.class="spinner" wire:target="openCart"></span>
                <span wire:target="openCart">{{__t('Товар у кошику')}}</span>
            </button>
        @endif --}}
    @else
        @if($product->isAvailability())
        <button class="main-btn blue-small icon-left mt-24 js-lw-modal" data-component="product.availability-order" data-product_id="{{$product->id}}">
            <span wire:loading.class="spinner"></span>
            <span>{{__t('Повідомити про наявність')}}</span>
        </button>
        @endif
    @endif
</span>
