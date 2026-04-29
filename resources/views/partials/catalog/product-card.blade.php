<div wire:key="product-{{ $product->id }}"
     class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">

    @if($product->picture)
        <a href="{{ $product->getUrl() }}" class="block">
            <img src="{{ $product->picture }}" alt="{{ $product->t('title') }}"
                 class="w-full h-48 object-cover" loading="lazy">
        </a>
    @else
        <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
            <span class="text-gray-400 text-sm">{{ __t('Без фото') }}</span>
        </div>
    @endif

    <div class="p-4">
        <a href="{{ $product->getUrl() }}" class="block">
            <h3 class="font-medium text-sm text-gray-800 mb-2 line-clamp-2 hover:text-blue-600 transition-colors">
                {{ $product->t('title') }}
            </h3>
        </a>

        <div class="mt-3">
            <span class="text-lg font-bold text-blue-600">
                {{ number_format((float) $product->price, 0, '.', ' ') }} {{ setting('currency') }}
            </span>
            @if($product->price_old && $product->price_old > $product->price)
                <span class="block text-xs text-gray-400 line-through">
                    {{ number_format((float) $product->price_old, 0, '.', ' ') }} {{ setting('currency') }}
                </span>
            @endif
        </div>

        <a href="{{ $product->getUrl() }}"
           class="mt-4 block w-full bg-blue-600 text-white py-2 rounded text-center text-sm font-medium hover:bg-blue-700 transition-colors">
            {{ __t('Детальніше') }}
        </a>
    </div>
</div>
