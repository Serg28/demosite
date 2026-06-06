@props(['item', 'product' => null])

@php
    $unitPrice = (float) $item->price;
    $itemName  = is_array($item->name) ? ($item->name[app()->getLocale()] ?? reset($item->name)) : (string) $item->name;
@endphp

<div wire:key="cart-item-{{ $item->rowId }}"
     data-qty="{{ $item->qty }}"
     x-data="cartStepper({ rowId: '{{ $item->rowId }}', unitPrice: {{ $unitPrice }} })"
     class="flex gap-4 py-4 border-b border-gray-100 last:border-0">

    {{-- Фото --}}
    <div class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden bg-surface">
        @if($product?->picture)
            <img src="{{ $product->picture }}"
                 alt="{{ $itemName }}"
                 class="w-full h-full object-cover"
                 loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif
    </div>

    {{-- Інфо --}}
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-ink truncate">{{ $itemName }}</p>
        <p class="text-xs text-gray-500 mt-0.5">@money($unitPrice) {{ setting('currency') }}</p>

        {{-- Оптова ціна: наступний рівень --}}
        {{-- @php $tiers = $product?->getWholesalePrices(); $nextTier = null;
             if ($tiers) {
                 foreach ($tiers as $minQty => $tierPrice) {
                     if ($minQty > $item->qty) { $nextTier = ['qty' => $minQty, 'price' => $tierPrice, 'left' => $minQty - $item->qty]; break; }
                 }
             }
        @endphp
        @if($nextTier)
            <p class="text-xs text-brand mt-0.5">
                {{ __t('Від') }} {{ $nextTier['qty'] }} {{ __t('шт.') }} — @money($nextTier['price']) {{ setting('currency') }}
                ({{ __t('ще') }} {{ $nextTier['left'] }} {{ __t('шт.') }})
            </p>
        @endif --}}

        {{-- Кількість --}}
        <div class="flex items-center gap-2 mt-2">
            <button type="button"
                    @click="dec"
                    class="w-6 h-6 rounded border border-gray-200 flex items-center justify-center text-gray-500 hover:border-brand hover:text-brand transition-colors">
                <span class="sr-only">-</span>&minus;
            </button>

            <input type="number"
                   x-model.number="qty"
                   @change="_save"
                   min="1"
                   class="w-10 text-center text-sm border border-gray-200 rounded py-0.5">

            <button type="button"
                    @click="inc"
                    class="w-6 h-6 rounded border border-gray-200 flex items-center justify-center text-gray-500 hover:border-brand hover:text-brand transition-colors">
                <span class="sr-only">+</span>+
            </button>
        </div>
    </div>

    {{-- Ціна + видалити --}}
    <div class="flex flex-col items-end gap-2">
        {{-- Реактивна ціна (Alpine) — оновлюється без очікування Livewire --}}
        <span x-text="totalFormatted + ' {{ setting('currency') }}'"
              class="text-sm font-semibold text-ink whitespace-nowrap"></span>

        <button type="button"
                wire:click="remove('{{ $item->rowId }}')"
                wire:loading.attr="disabled"
                wire:target="remove('{{ $item->rowId }}')"
                class="text-gray-400 hover:text-red-500 transition-colors"
                aria-label="{{ __t('Видалити') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
