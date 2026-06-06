@props([
    'product',
    'count'        => 1,
    'alpineCount'  => null,  // Alpine вираз для реактивної кількості (напр. 'qty')
    'showCartIcon' => true,
    'iconOnly'     => false, // true = тільки іконка (для компактних карток)
    'class'        => '',
])

@if($product->isActiveForOrder())
    <button type="button"
            class="btn-buy {{ $class }}"
            data-js-add-to-cart
            data-id="{{ $product->id }}"
            data-sum="{{ $product->getPrice() }}"
            {!! $alpineCount ? ':data-count="' . $alpineCount . '"' : 'data-count="' . $count . '"' !!}
            data-options=""
            @if(setting('checkbox_google_analytics_four')) data-ga4-addtocart @endif>
        <span data-js-spinner class="hidden">
            <svg class="animate-spin h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </span>
        @if($showCartIcon)
            <span @class(['icon', 'sr-only' => false])>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </span>
        @endif
        @unless($iconOnly)
            <span>{{ __t('В кошик') }}</span>
        @endunless
    </button>

@elseif($product->isAvailability())
    <button type="button"
            class="btn-buy btn-notify {{ $class }}"
            data-js-modal
            data-component="product.availability-order"
            data-product_id="{{ $product->id }}">
        <span data-js-spinner class="hidden"></span>
        <span>{{ __t('Повідомити про наявність') }}</span>
    </button>

@elseif($product->isActiveForPreOrder())
    <button type="button"
            class="btn-buy btn-preorder {{ $class }}"
            data-js-modal
            data-component="product.order-on-demand"
            data-product_id="{{ $product->id }}">
        <span data-js-spinner class="hidden"></span>
        <span>{{ __t('Передзамовлення') }}</span>
    </button>

@else
    <button type="button" class="btn-buy btn-disabled {{ $class }}" disabled>
        <span>{{ __t('Недоступний') }}</span>
    </button>
@endif
