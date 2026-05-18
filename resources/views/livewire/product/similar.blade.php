<div>
    @if($list->isNotEmpty())
        <h3 class="font-bold text-lg mb-4">{{ __t('Разом з цим купують') }}</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($list as $item)
                <a
                    href="{{ route('product.show', $item->slug) }}"
                    wire:key="similar-{{ $item->id }}"
                    class="group bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition"
                >
                    <div class="aspect-square bg-gray-50 overflow-hidden">
                        @if($item->picture)
                            <img
                                src="{{ $item->picture }}"
                                alt="{{ $item->t('title') }}"
                                class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                            />
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-3">
                        <p class="text-sm text-gray-800 font-medium line-clamp-2 mb-2">{{ $item->t('title') }}</p>
                        <div class="flex items-baseline gap-1">
                            <span class="font-bold text-gray-900">@money($item->price)</span>
                            @if($item->hasDiscount())
                                <span class="text-xs text-gray-400 line-through">@money($item->price_old)</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
