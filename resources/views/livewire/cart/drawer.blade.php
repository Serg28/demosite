<div>
    {{-- Overlay --}}
    <div x-show="$wire.isOpen"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 z-40"
         wire:click="close"
         style="display: none;">
    </div>

    {{-- Drawer панель --}}
    <div x-show="$wire.isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white shadow-2xl flex flex-col"
         style="display: none;">

        {{-- Заголовок --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-ink">{{ __t('Кошик') }}</h2>
            <button type="button" wire:click="close" class="p-2 text-gray-400 hover:text-ink transition-colors" aria-label="{{ __t('Закрити') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Список товарів --}}
        <div class="flex-1 overflow-y-auto px-6" wire:loading.class="opacity-50">
            @if($items->isEmpty())
                <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    <p class="text-sm">{{ __t('Кошик порожній') }}</p>
                </div>
            @else
                @foreach($items as $item)
                    <x-cart.item :item="$item" :product="$products[$item->id] ?? null" />
                @endforeach
            @endif
        </div>

        {{-- Підсумок + CTA --}}
        @if($items->isNotEmpty())
            <x-cart.summary :total="$total" :subtotal="$subtotal" />
        @endif
    </div>
</div>
