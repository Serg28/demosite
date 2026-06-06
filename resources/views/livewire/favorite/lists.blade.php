<div>
    {{-- Header --}}
    <div class="card p-5 mb-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h1 class="font-bold text-xl">
                {{ __t('Список бажань') }}
                @if($this->totalCount > 0)
                    <span class="text-ink-muted font-normal text-base">({{ $this->totalCount }})</span>
                @endif
            </h1>
            @if($this->products->isNotEmpty())
                @php
                    $allItems = $this->products->map(fn ($p) => ['id' => $p->id, 'count' => 1])->values()->toJson();
                @endphp
                <button type="button"
                        data-js-add-all-to-cart
                        data-items="{{ e($allItems) }}"
                        class="btn btn-p text-sm">
                    {{ __t('Все в кошик') }}
                </button>
            @endif
        </div>
    </div>

    @if($this->products->isEmpty())
        <div class="card p-16 text-center">
            <div class="text-5xl mb-4">🤍</div>
            <p class="text-ink-muted mb-4">{{ __t('Список бажань порожній') }}</p>
            <a href="{{ route('catalog.show', 'all') }}" class="btn btn-p">
                {{ __t('До каталогу') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($this->products as $product)
                @php
                    $title = $product->t('title');
                    $url   = $product->getUrl();
                    $price = $product->getPrice();
                @endphp
                <div wire:key="fav-{{ $product->id }}"
                     class="card overflow-hidden hover:shadow-md transition-shadow relative">

                    {{-- Кнопка видалення --}}
                    <button type="button"
                            data-js-like
                            data-id="{{ $product->id }}"
                            class="like-button active absolute top-2 right-2 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-white shadow text-red-400 hover:text-red-600 transition-colors"
                            title="{{ __t('Видалити з вибраного') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </button>

                    {{-- Фото --}}
                    <a href="{{ $url }}" class="block overflow-hidden">
                        @if($product->picture)
                            <img src="{{ $product->picture }}"
                                 alt="{{ e($title) }}"
                                 class="w-full h-44 object-cover"
                                 loading="lazy">
                        @else
                            <div class="w-full h-44 bg-gray-100 flex items-center justify-center">
                                <span class="text-gray-400 text-sm">{{ __t('Без фото') }}</span>
                            </div>
                        @endif
                    </a>

                    <div class="p-4">
                        <a href="{{ $url }}"
                           class="block font-medium text-sm text-gray-800 mb-2 line-clamp-2 hover:text-brand transition-colors">
                            {{ $title }}
                        </a>

                        <div class="mt-2">
                            <span class="text-lg font-bold text-brand">
                                @money($price, 0) {{ setting('currency') }}
                            </span>
                            @if($product->hasDiscount())
                                <span class="block text-xs text-gray-400 line-through">
                                    @money($product->getPriceOld(), 0) {{ setting('currency') }}
                                </span>
                            @endif
                        </div>

                        <div class="mt-3">
                            <x-buy-button :product="$product" :showCartIcon="true" :count="1"
                                          class="w-full flex items-center justify-center" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $this->products->links() }}
        </div>
    @endif
</div>
