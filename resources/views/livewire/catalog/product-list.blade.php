@php
    $result      = $this->products;
    $items       = $result['products'] ?? [];
    $total       = $result['total'] ?? 0;
    $currentPage = $result['current_page'] ?? 1;
    $lastPage    = $result['last_page'] ?? 1;
    $hasMore     = $result['has_more'] ?? false;
    $perPage     = $result['per_page'] ?? 24;

    // Smart page range: always show first, last and window around current
    $window = 2;
    $pages  = collect(range(1, $lastPage));
    $shown  = $pages->filter(fn($p) =>
        $p === 1 || $p === $lastPage || abs($p - $currentPage) <= $window
    )->values();
@endphp

@php
    $alpineConfig = htmlspecialchars(json_encode([
        'hasMore'    => $hasMore,
        'nextPage'   => $currentPage + 1,
        'categoryId' => $this->category->id,
        'filters'    => $filters,
        'sortBy'     => $sortBy,
        'sortDir'    => $sortDir,
        'perPage'    => $perPage,
    ]), ENT_QUOTES, 'UTF-8');
@endphp

<div
    x-data="catalogProductList($el)"
    data-config="{{ $alpineConfig }}"
    wire:key="product-list-{{ $currentPage }}"
>
    {{-- Result count --}}
    <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
        <p>
            {{ __t('Показано') }}
            <span x-text="{{ count($items) }} + extraProducts.length">{{ count($items) }}</span>
            {{ __t('з') }} {{ number_format($total) }} {{ __t('товарів') }}
        </p>
    </div>

    {{-- Product grid --}}
    <div class="relative">
    {{-- Filtering overlay --}}
    <div
        wire:loading
        wire:target="applyFilters,applySort,setPage"
        class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/60"
    >
        <div class="flex items-center gap-3 rounded-full bg-white px-5 py-2.5 shadow-md">
            <svg class="h-5 w-5 animate-spin text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span class="text-sm font-medium text-gray-600">{{ __t('Завантаження...') }}</span>
        </div>
    </div>
    <div wire:loading.class="opacity-40 pointer-events-none" wire:target="applyFilters,applySort,setPage" class="transition-opacity duration-200">

        @if(count($items) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                {{-- Server-rendered first page (SEO-friendly) --}}
                @foreach($items as $product)
                    @php
                        $title = is_array($product->title)
                            ? ($product->title['ua'] ?? $product->title['ru'] ?? '')
                            : (string) $product->title;
                    @endphp
                    <div wire:key="product-{{ $product->id }}"
                         class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        @if($product->picture)
                            <a href="/products/{{ $product->slug }}" class="block">
                                <img
                                    src="{{ $product->picture }}"
                                    alt="{{ $title }}"
                                    class="w-full h-48 object-cover"
                                    loading="lazy"
                                >
                            </a>
                        @else
                            <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                                <span class="text-gray-400 text-sm">{{ __t('Без фото') }}</span>
                            </div>
                        @endif

                        <div class="p-4">
                            <a href="/products/{{ $product->slug }}" class="block">
                                <h3 class="font-medium text-sm text-gray-800 mb-2 line-clamp-2 hover:text-blue-600 transition-colors">
                                    {{ $title }}
                                </h3>
                            </a>

                            <div class="mt-3">
                                <span class="text-lg font-bold text-blue-600">
                                    {{ number_format((float) $product->price, 0, '.', ' ') }} {{ __t('грн') }}
                                </span>
                                @if($product->price_old && $product->price_old > $product->price)
                                    <span class="block text-xs text-gray-400 line-through">
                                        {{ number_format((float) $product->price_old, 0, '.', ' ') }} {{ __t('грн') }}
                                    </span>
                                @endif
                            </div>

                            <a href="/products/{{ $product->slug }}"
                               class="mt-4 block w-full bg-blue-600 text-white py-2 rounded text-center text-sm font-medium hover:bg-blue-700 transition-colors">
                                {{ __t('Детальніше') }}
                            </a>
                        </div>
                    </div>
                @endforeach

                {{-- Alpine-appended extra products (show more, no Livewire state) --}}
                <template x-for="product in extraProducts" :key="'extra-' + product.id">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        <template x-if="product.picture">
                            <a :href="'/products/' + product.slug" class="block">
                                <img
                                    :src="product.picture"
                                    :alt="getTitle(product)"
                                    class="w-full h-48 object-cover"
                                    loading="lazy"
                                >
                            </a>
                        </template>
                        <template x-if="!product.picture">
                            <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                                <span class="text-gray-400 text-sm">{{ __t('Без фото') }}</span>
                            </div>
                        </template>

                        <div class="p-4">
                            <a :href="'/products/' + product.slug" class="block">
                                <h3 class="font-medium text-sm text-gray-800 mb-2 line-clamp-2 hover:text-blue-600 transition-colors"
                                    x-text="getTitle(product)"></h3>
                            </a>

                            <div class="mt-3">
                                <span class="text-lg font-bold text-blue-600" x-text="formatPrice(product.price)"></span>
                                <template x-if="product.price_old && Number(product.price_old) > Number(product.price)">
                                    <span class="block text-xs text-gray-400 line-through"
                                          x-text="formatPrice(product.price_old)"></span>
                                </template>
                            </div>

                            <a :href="'/products/' + product.slug"
                               class="mt-4 block w-full bg-blue-600 text-white py-2 rounded text-center text-sm font-medium hover:bg-blue-700 transition-colors">
                                {{ __t('Детальніше') }}
                            </a>
                        </div>
                    </div>
                </template>

            </div>

            {{-- ── Bottom controls ─────────────────────────────────────────────── --}}
            <div class="mt-10 space-y-6">

                {{-- Show more button (Alpine, no Livewire state) --}}
                <div class="flex justify-center" x-show="hasMore" x-cloak>
                    <button
                        @click="loadMore()"
                        :disabled="loading"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg x-show="loading" class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="loading ? '{{ __t('Завантаження...') }}' : '{{ __t('Показати ще') }}'"></span>
                    </button>
                </div>

                {{-- Numbered pagination (Livewire wire:click, classic behaviour) --}}
                @if($lastPage > 1)
                    <nav aria-label="{{ __t('Навігація по сторінках') }}" class="flex justify-center">
                        <ul class="flex items-center gap-1">

                            {{-- Previous --}}
                            <li>
                                @if($currentPage > 1)
                                    <button
                                        wire:click="setPage({{ $currentPage - 1 }})"
                                        wire:loading.attr="disabled"
                                        class="flex items-center justify-center w-9 h-9 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 hover:border-gray-400 transition"
                                        aria-label="{{ __t('Попередня') }}"
                                    >
                                        ←
                                    </button>
                                @else
                                    <span class="flex items-center justify-center w-9 h-9 rounded border border-gray-200 text-sm text-gray-300 cursor-not-allowed">←</span>
                                @endif
                            </li>

                            {{-- Page numbers with smart ellipsis --}}
                            @php $prevPage = null; @endphp
                            @foreach($shown as $p)
                                @if($prevPage !== null && $p - $prevPage > 1)
                                    <li>
                                        <span class="flex items-center justify-center w-9 h-9 text-sm text-gray-400">…</span>
                                    </li>
                                @endif
                                <li>
                                    @if($p === $currentPage)
                                        <span
                                            aria-current="page"
                                            class="flex items-center justify-center w-9 h-9 rounded border border-blue-500 bg-blue-500 text-sm font-semibold text-white"
                                        >{{ $p }}</span>
                                    @else
                                        <button
                                            wire:click="setPage({{ $p }})"
                                            wire:loading.attr="disabled"
                                            class="flex items-center justify-center w-9 h-9 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 hover:border-gray-400 transition"
                                        >{{ $p }}</button>
                                    @endif
                                </li>
                                @php $prevPage = $p; @endphp
                            @endforeach

                            {{-- Next --}}
                            <li>
                                @if($currentPage < $lastPage)
                                    <button
                                        wire:click="setPage({{ $currentPage + 1 }})"
                                        wire:loading.attr="disabled"
                                        class="flex items-center justify-center w-9 h-9 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 hover:border-gray-400 transition"
                                        aria-label="{{ __t('Наступна') }}"
                                    >
                                        →
                                    </button>
                                @else
                                    <span class="flex items-center justify-center w-9 h-9 rounded border border-gray-200 text-sm text-gray-300 cursor-not-allowed">→</span>
                                @endif
                            </li>

                        </ul>
                    </nav>
                @endif

            </div>

        @else
            <div class="text-center py-16">
                <svg class="mx-auto mb-4 text-gray-300 w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-500 text-lg font-medium">{{ __t('Товари не знайдені') }}</p>
                <p class="text-gray-400 text-sm mt-1">{{ __t('Спробуйте змінити фільтри') }}</p>
            </div>
        @endif

    </div>{{-- end wire:loading.class --}}
    </div>{{-- end .relative --}}

</div>
