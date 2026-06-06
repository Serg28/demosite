<div>
    {{-- Header --}}
    <div class="card p-5 mb-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h1 class="font-bold text-xl">
                {{ __t('Порівняння товарів') }}
                @if($this->totalCount > 0)
                    <span class="text-ink-muted font-normal text-base">({{ $this->totalCount }})</span>
                @endif
            </h1>
            @if($this->products->isNotEmpty())
                <button wire:click="clear" class="btn btn-outline text-sm text-red-500 border-red-200 hover:bg-red-50">
                    {{ __t('Очистити') }}
                </button>
            @endif
        </div>
    </div>

    @if($this->products->isEmpty())
        <div class="card p-16 text-center">
            <div class="text-5xl mb-4">⇄</div>
            <p class="text-ink-muted mb-4">{{ __t('Немає товарів для порівняння') }}</p>
            <a href="{{ route('catalog.show', 'all') }}" class="btn btn-p">
                {{ __t('До каталогу') }}
            </a>
        </div>
    @else
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left p-4 font-semibold text-ink-muted w-32">{{ __t('Параметр') }}</th>
                        @foreach($this->products as $product)
                            @php
                                $title = $product->t('title');
                                $url   = $product->getUrl();
                            @endphp
                            <th class="p-4 text-center align-top w-48" wire:key="th-{{ $product->id }}">
                                <div class="relative">
                                    {{-- Кнопка видалення --}}
                                    <button type="button"
                                            data-js-compare
                                            data-id="{{ $product->id }}"
                                            class="compare-button active absolute -top-1 -right-1 w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 hover:bg-red-100 text-ink-muted hover:text-red-500 transition-colors"
                                            title="{{ __t('Видалити') }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>

                                    {{-- Фото --}}
                                    <a href="{{ $url }}" class="block mb-2">
                                        @if($product->picture)
                                            <img src="{{ $product->picture }}"
                                                 alt="{{ e($title) }}"
                                                 class="w-full h-32 object-cover rounded"
                                                 loading="lazy">
                                        @else
                                            <div class="w-full h-32 bg-gray-100 rounded flex items-center justify-center">
                                                <span class="text-gray-400 text-xs">{{ __t('Без фото') }}</span>
                                            </div>
                                        @endif
                                    </a>

                                    <a href="{{ $url }}"
                                       class="block font-medium text-xs line-clamp-2 hover:text-brand transition-colors">
                                        {{ $title }}
                                    </a>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    {{-- Ціна --}}
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 text-ink-muted font-medium">{{ __t('Ціна') }}</td>
                        @foreach($this->products as $product)
                            <td class="p-4 text-center" wire:key="price-{{ $product->id }}">
                                <span class="font-bold text-brand">
                                    @money($product->getPrice(), 0) {{ setting('currency') }}
                                </span>
                                @if($product->hasDiscount())
                                    <span class="block text-xs text-gray-400 line-through">
                                        @money($product->getPriceOld(), 0) {{ setting('currency') }}
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    {{-- Артикул --}}
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 text-ink-muted font-medium">{{ __t('Артикул') }}</td>
                        @foreach($this->products as $product)
                            <td class="p-4 text-center text-ink-muted text-xs" wire:key="code-{{ $product->id }}">
                                {{ $product->code ?: '—' }}
                            </td>
                        @endforeach
                    </tr>

                    {{-- Категорія --}}
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 text-ink-muted font-medium">{{ __t('Категорія') }}</td>
                        @foreach($this->products as $product)
                            <td class="p-4 text-center text-xs" wire:key="cat-{{ $product->id }}">
                                {{ $product->category?->t('title') ?: '—' }}
                            </td>
                        @endforeach
                    </tr>

                    {{-- Наявність --}}
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 text-ink-muted font-medium">{{ __t('Наявність') }}</td>
                        @foreach($this->products as $product)
                            <td class="p-4 text-center" wire:key="qty-{{ $product->id }}">
                                @if($product->quantity > 0)
                                    <span class="text-green-600 text-xs font-medium">{{ __t('В наявності') }}</span>
                                @else
                                    <span class="text-gray-400 text-xs">{{ __t('Немає') }}</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    {{-- Дії --}}
                    <tr>
                        <td class="p-4"></td>
                        @foreach($this->products as $product)
                            <td class="p-4 text-center" wire:key="btn-{{ $product->id }}">
                                <x-buy-button :product="$product" :showCartIcon="false" :count="1"
                                              class="w-full text-xs" />
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>
