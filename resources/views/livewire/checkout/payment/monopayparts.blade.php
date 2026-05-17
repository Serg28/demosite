<div class="mt-4 space-y-3">
    <livewire:checkout.pay-parts-calculator
        :gatewaySlug="$gateway"
        :orderAmount="$orderAmount"
        :wire:key="'ppc-'.$payMethodId.'-'.(int)$orderAmount"
    />

    <div>
        <label class="text-sm font-medium mb-1.5 block">{{ __t('Кількість місяців') }} *</label>
        <select wire:model.live="payPartsCount" class="field @error('payPartsCount') border-red-400 @enderror">
            <option value="">{{ __t('Оберіть...') }}</option>
            @foreach(range(2, config('checkout.payment_parts_months.monopayparts', 12)) as $m)
                <option value="{{ $m }}">{{ $m }} {{ __t('міс.') }}</option>
            @endforeach
        </select>
        @error('payPartsCount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
