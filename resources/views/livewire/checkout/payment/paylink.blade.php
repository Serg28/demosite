<div class="mt-4 space-y-3">
    <div>
        <label class="text-sm font-medium mb-1.5 block">{{ __t('Назва компанії') }} *</label>
        <input wire:model.live.debounce.500ms="b2bCompany"
               type="text"
               class="field @error('b2bCompany') border-red-400 @enderror"
               placeholder="{{ __t('ТОВ «Назва»') }}">
        @error('b2bCompany')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="text-sm font-medium mb-1.5 block">{{ __t('ЄДРПОУ') }} *</label>
        <input wire:model.live="b2bEdrpou"
               type="text"
               inputmode="numeric"
               class="field @error('b2bEdrpou') border-red-400 @enderror"
               placeholder="12345678"
               maxlength="8">
        @error('b2bEdrpou')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
