@php
    $title = $product->t('title');
    $url   = $product->getUrl();
    $price = $product->getPrice();
@endphp
<div wire:key="product-{{ $product->id }}"
     data-ga-push="0"
     data-code="{{ $product->code }}"
     data-sum="{{ $price }}"
     class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">

    {{-- Фото --}}
    <a href="{{ $url }}" class="block overflow-hidden">
        @if($product->picture)
            <img src="{{ $product->picture }}"
                 alt="{{ e($title) }}"
                 class="w-full h-48 object-cover"
                 loading="lazy">
        @else
            <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                <span class="text-gray-400 text-sm">{{ __t('Без фото') }}</span>
            </div>
        @endif
    </a>

    <div class="p-4">
        {{-- Назва (data-ga4-click = select_item) --}}
        <a href="{{ $url }}"
           class="block font-medium text-sm text-gray-800 mb-2 line-clamp-2 hover:text-brand transition-colors"
           data-ga4-click>
            {{ $title }}
        </a>

        {{-- Ціна --}}
        <div class="mt-3">
            <span class="text-lg font-bold text-brand">
                @money($price, 0) {{ setting('currency') }}
            </span>
            @if($product->hasDiscount())
                <span class="block text-xs text-gray-400 line-through">
                    @money($product->getPriceOld(), 0) {{ setting('currency') }}
                </span>
            @endif
        </div>

        {{-- CTA --}}
        <div class="mt-4 flex gap-2">
            <a href="{{ $url }}"
               class="flex-1 bg-brand text-white py-2 rounded text-center text-sm font-medium hover:bg-brand/90 transition-colors">
                {{ __t('Детальніше') }}
            </a>

            <x-buy-button :product="$product" :showCartIcon="true" :count="1"
                          class="flex items-center justify-center w-10 h-9 rounded" />
        </div>
    </div>
</div>
