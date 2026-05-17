@props(['delivery', 'selected' => false])

<label class="flex items-center justify-between p-4 border-2 rounded-xl cursor-pointer transition-colors
    {{ $selected ? 'border-brand bg-brand-light' : 'border-gray-100 hover:border-gray-200' }}">
    <div class="flex items-center gap-3">
        <input type="radio"
               wire:model.live="deliveryId"
               value="{{ $delivery->id }}"
               class="accent-brand">
        <div>
            <p class="text-sm font-semibold">{{ $delivery->t('title') }}</p>
            @if($delivery->free_cost)
                <p class="text-xs text-ink-muted">
                    {{ __t('Безкоштовно від :amount', ['amount' => number_format($delivery->free_cost, 0, '.', ' ')]) }}
                </p>
            @endif
        </div>
    </div>
    <span class="text-sm font-semibold {{ $delivery->price == 0 ? 'instock' : 'text-ink-muted' }}">
        @if($delivery->price == 0)
            {{ __t('Безкоштовно') }}
        @else
            ~@money($delivery->price, 0)
        @endif
    </span>
</label>
