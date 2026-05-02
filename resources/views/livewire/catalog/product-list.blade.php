@php $paginator = $this->products; @endphp

<div>
    <div class="mb-4 text-sm text-gray-500">
        <p>{{ __t('Показано') }} {{ $paginator->count() }} {{ __t('з') }} {{ number_format($paginator->total()) }} {{ __t('товарів') }}</p>
    </div>

    <div class="relative">
        <div wire:loading wire:target="applyFilters"
             class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/60">
            <div class="flex items-center gap-3 rounded-full bg-white px-5 py-2.5 shadow-md">
                <svg class="h-5 w-5 animate-spin text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-600">{{ __t('Завантаження...') }}</span>
            </div>
        </div>

        <div wire:loading.class="opacity-40 pointer-events-none" wire:target="applyFilters"
             class="transition-opacity duration-200">
            @if($paginator->isNotEmpty())
                <div id="js-product-grid"
                     data-js-product-grid
                     data-item_list_name="{{ $this->category->t('title') }}"
                     class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                     data-catalog-url="{{ $this->category->getUrl() }}"
                     data-api-url="{{ route('api.v1.catalog.products-html', $this->category) }}">
                    @foreach($paginator as $product)
                        @include('partials.catalog.product-card', compact('product'))
                    @endforeach
                </div>

                {{ $paginator->links('catalog.pagination') }}
            @else
                <div class="py-16 text-center">
                    <svg class="mx-auto mb-4 h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-lg font-medium text-gray-500">{{ __t('Товари не знайдені') }}</p>
                    <p class="mt-1 text-sm text-gray-400">{{ __t('Спробуй змінити фільтри') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
