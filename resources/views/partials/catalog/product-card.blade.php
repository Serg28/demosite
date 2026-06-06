@php
    $title = $product->t('title');
    $url   = $product->getUrl();
    $price = $product->getPrice();
@endphp

{{-- Grid / List — вид керується через Alpine.store('catalog').view --}}
<div wire:key="product-{{ $product->id }}"
     data-ga-push="0"
     data-code="{{ $product->code }}"
     data-sum="{{ $price }}">

    {{-- ═══ GRID вид ══════════════════════════════════ --}}
    <div x-show="$store.catalog.view !== 'list'"
         x-cloak
         class="card p-4 hover:shadow-md group flex flex-col h-full">

        {{-- Фото --}}
        <div class="relative mb-3">
            <a href="{{ $url }}" class="block">
                @if($product->picture)
                    <img src="{{ $product->picture }}"
                         alt="{{ e($title) }}"
                         class="w-full aspect-square object-cover rounded-xl"
                         loading="lazy">
                @else
                    <div class="w-full aspect-square rounded-xl bg-surface flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </a>

            {{-- Бейджи --}}
            <div class="absolute top-2 left-2 flex flex-col gap-1">
                @if($product->hasDiscount())
                    <span class="badge bdg-sale">-{{ $product->getPriceDifferencePercentage() }}%</span>
                @endif
            </div>

            {{-- Кнопка вибраного (з'являється при hover) --}}
            <button type="button"
                    data-js-like
                    data-id="{{ $product->id }}"
                    class="like-button absolute top-2 right-2 w-7 h-7 bg-white rounded-full shadow opacity-0 group-hover:opacity-100 flex items-center justify-center text-sm transition-all"
                    title="{{ __t('До вибраного') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        </div>

        {{-- Назва + статус --}}
        <a href="{{ $url }}" class="flex-1" data-ga4-click>
            <p class="text-sm font-medium leading-snug line-clamp-2 mb-2 hover:text-brand transition-colors">{{ $title }}</p>
            @if($product->isActiveForOrder())
                <p class="text-xs instock mb-2">● {{ __t('В наявності') }}</p>
            @else
                <p class="text-xs nostock mb-2">● {{ __t('Немає') }}</p>
            @endif
        </a>

        {{-- Ціна + дії --}}
        <div class="flex items-center justify-between gap-2 mt-auto pt-2">
            <div>
                <span class="font-bold text-ink">@money($price, 0) {{ setting('currency') }}</span>
                @if($product->hasDiscount())
                    <span class="block text-xs text-ink-light line-through">@money($product->getPriceOld(), 0) {{ setting('currency') }}</span>
                @endif
            </div>
            <div class="flex items-center gap-1">
                <button type="button"
                        data-js-compare
                        data-id="{{ $product->id }}"
                        class="compare-button w-8 h-8 border border-gray-200 rounded-lg flex items-center justify-center text-gray-400 hover:border-brand hover:text-brand transition-colors"
                        title="{{ __t('До порівняння') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01"/>
                    </svg>
                </button>
                @if($product->isActiveForOrder())
                    <x-buy-button :product="$product" :showCartIcon="true" :iconOnly="true" :count="1"
                                  class="w-9 h-9 bg-brand hover:bg-brand-dark rounded-lg flex items-center justify-center text-white" />
                @else
                    <button type="button"
                            data-js-preorder
                            data-id="{{ $product->id }}"
                            class="w-9 h-9 bg-gray-100 hover:bg-orange-50 border border-gray-200 hover:border-orange-300 rounded-lg flex items-center justify-center text-gray-400 hover:text-orange-500 transition-colors"
                            title="{{ __t('Товар під замовлення') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ LIST вид ═══════════════════════════════════ --}}
    <div x-show="$store.catalog.view === 'list'"
         x-cloak
         class="card p-4 flex gap-4 hover:shadow-md">

        <a href="{{ $url }}" class="w-20 h-20 flex-shrink-0">
            @if($product->picture)
                <img src="{{ $product->picture }}"
                     alt="{{ e($title) }}"
                     class="w-20 h-20 rounded-xl object-cover"
                     loading="lazy">
            @else
                <div class="w-20 h-20 rounded-xl bg-surface flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
        </a>

        <div class="flex-1 min-w-0">
            <a href="{{ $url }}"
               class="font-semibold text-sm hover:text-brand transition-colors block mb-1 line-clamp-2"
               data-ga4-click>{{ $title }}</a>
            @if($product->code)
                <p class="text-xs text-ink-muted">{{ __t('Код') }}: {{ $product->code }}</p>
            @endif
            @if($product->isActiveForOrder())
                <p class="text-xs instock mt-1">● {{ __t('В наявності') }}</p>
            @else
                <p class="text-xs nostock mt-1">● {{ __t('Немає') }}</p>
            @endif
        </div>

        <div class="flex flex-col items-end gap-2 flex-shrink-0">
            <div class="text-right">
                <div class="font-bold text-ink">@money($price, 0) {{ setting('currency') }}</div>
                @if($product->hasDiscount())
                    <div class="text-xs text-ink-light line-through">@money($product->getPriceOld(), 0) {{ setting('currency') }}</div>
                @endif
            </div>
            <div class="flex items-center gap-1">
                <button type="button"
                        data-js-like
                        data-id="{{ $product->id }}"
                        class="like-button w-8 h-8 border border-gray-200 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-400 hover:border-red-200 transition-colors"
                        title="{{ __t('До вибраного') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
                <button type="button"
                        data-js-compare
                        data-id="{{ $product->id }}"
                        class="compare-button w-8 h-8 border border-gray-200 rounded-lg flex items-center justify-center text-gray-400 hover:border-brand hover:text-brand transition-colors"
                        title="{{ __t('До порівняння') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01"/>
                    </svg>
                </button>
                @if($product->isActiveForOrder())
                    <x-buy-button :product="$product" :showCartIcon="true" :count="1"
                                  class="btn btn-p btn-sm" />
                @else
                    <button type="button"
                            data-js-preorder
                            data-id="{{ $product->id }}"
                            class="w-8 h-8 border border-gray-200 hover:border-orange-300 rounded-lg flex items-center justify-center text-gray-400 hover:text-orange-500 transition-colors"
                            title="{{ __t('Товар під замовлення') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>

</div>
