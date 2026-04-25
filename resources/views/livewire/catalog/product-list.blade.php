<div class="product-grid">
    @if($products && isset($products['products']) && count($products['products']) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($products['products'] as $product)
                <div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    @if($product['picture'])
                        <img
                            src="{{ $product['picture'] }}"
                            alt="{{ $product['title'] }}"
                            class="w-full h-64 object-cover"
                        >
                    @else
                        <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">No image</span>
                        </div>
                    @endif

                    <div class="p-4">
                        <h3 class="font-semibold text-sm mb-2 line-clamp-2">
                            {{ $product['title'] }}
                        </h3>

                        <p class="text-gray-600 text-sm mb-4">
                            {{ $product['short_description'] ?? '' }}
                        </p>

                        <div class="flex items-center justify-between mb-4">
                            <span class="text-lg font-bold text-blue-600">
                                {{ number_format($product['price'], 2) }} ₴
                            </span>
                            @if($product['price_old'])
                                <span class="text-sm text-gray-500 line-through">
                                    {{ number_format($product['price_old'], 2) }} ₴
                                </span>
                            @endif
                        </div>

                        <a
                            href="/products/{{ $product['slug'] }}"
                            class="w-full bg-blue-600 text-white py-2 rounded text-center text-sm font-medium hover:bg-blue-700 transition block"
                        >
                            Детальніше
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if(isset($products['pagination']))
            <div class="mt-8 flex justify-center">
                <nav class="flex gap-2">
                    @if($products['pagination']['current_page'] > 1)
                        <button wire:click="previousPage()" class="px-4 py-2 border rounded hover:bg-gray-100">
                            Попередня
                        </button>
                    @endif

                    @for($i = 1; $i <= $products['pagination']['last_page']; $i++)
                        <button
                            wire:click="gotoPage({{ $i }})"
                            class="px-4 py-2 border rounded {{ $i == $products['pagination']['current_page'] ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}"
                        >
                            {{ $i }}
                        </button>
                    @endfor

                    @if($products['pagination']['current_page'] < $products['pagination']['last_page'])
                        <button wire:click="nextPage()" class="px-4 py-2 border rounded hover:bg-gray-100">
                            Наступна
                        </button>
                    @endif
                </nav>
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">Товари не знайдені</p>
        </div>
    @endif
</div>
