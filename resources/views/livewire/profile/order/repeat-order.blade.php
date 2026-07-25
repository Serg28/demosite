<div class="p-6">

    <h2 class="text-xl font-bold mb-4">{{ __t('Повторити замовлення') }}</h2>

    @php
        $availableItems = collect($items)->filter(fn($i) => $i['available']);
        $unavailableCount = count($items) - $availableItems->count();
    @endphp

    @if(count($items) > 0)
        @if($unavailableCount > 0)
            <p class="text-xs text-amber-600 bg-amber-50 rounded-lg px-3 py-2 mb-4">
                {{ __t('Деякі товари відсутні в наявності і не будуть додані до кошика.') }}
            </p>
        @endif

        <div class="divide-y divide-gray-100 mb-5 max-h-72 overflow-y-auto">
            @foreach($items as $item)
                <div class="flex items-center gap-3 py-3 {{ !$item['available'] ? 'opacity-50' : '' }}">
                    @if($item['picture'])
                        <img src="{{ $item['picture'] }}"
                             class="w-12 h-12 rounded-lg object-cover border border-gray-100 flex-shrink-0"
                             alt="">
                    @else
                        <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-300 flex-shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9l4-4 4 4 4-4 4 4"/><circle cx="8.5" cy="13.5" r="1.5"/></svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium leading-snug line-clamp-2">{{ $item['title'] }}</p>
                        <p class="text-xs mt-0.5">
                            @if($item['available'])
                                <span class="text-ink-muted">{{ $item['count'] }} {{ __t('шт.') }} × @money($item['price']) {{ setting('currency') }}</span>
                            @else
                                <span class="text-amber-600">{{ __t('Немає в наявності') }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex gap-3 justify-end">
            <button wire:click="$dispatch('closeModal')" class="btn btn-o">
                {{ __t('Скасувати') }}
            </button>
            @if($availableItems->count() > 0)
                <button
                    data-js-add-all-to-cart
                    data-items="{{ json_encode($availableItems->map(fn($i) => ['id' => $i['id'], 'count' => $i['count']])->values()->all()) }}"
                    class="btn btn-p"
                >
                    🛒 {{ __t('Додати до кошика') }}
                    @if($unavailableCount > 0)
                        <span class="text-xs opacity-75 ml-1">({{ $availableItems->count() }} {{ __t('з') }} {{ count($items) }})</span>
                    @endif
                </button>
            @else
                <button wire:click="$dispatch('closeModal')" class="btn btn-p">
                    {{ __t('Закрити') }}
                </button>
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <p class="text-ink-muted">{{ __t('Товари цього замовлення недоступні.') }}</p>
            <button wire:click="$dispatch('closeModal')" class="btn btn-o mt-4">{{ __t('Закрити') }}</button>
        </div>
    @endif

</div>
