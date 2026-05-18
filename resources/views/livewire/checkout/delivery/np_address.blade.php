<div class="space-y-3">
    <div class="grid grid-cols-2 gap-3">
        <div class="col-span-2 sm:col-span-1">
            <label class="text-sm font-medium mb-1.5 block">{{ __t('Вулиця') }} <span class="text-red-500">*</span></label>
            <input wire:model.live.debounce.500ms="street"
                   type="text"
                   class="field"
                   placeholder="{{ __t('Назва вулиці') }}">
        </div>
        <div>
            <label class="text-sm font-medium mb-1.5 block">{{ __t('Будинок') }} <span class="text-red-500">*</span></label>
            <input wire:model.live.debounce.500ms="house"
                   type="text"
                   class="field"
                   placeholder="{{ __t('Номер будинку') }}">
        </div>
    </div>

    <div class="grid grid-cols-3 gap-3">
        <div>
            <label class="text-sm font-medium mb-1.5 block">{{ __t('Квартира') }}</label>
            <input wire:model.live.debounce.500ms="apartment"
                   type="text"
                   class="field"
                   placeholder="{{ __t('Кв.') }}">
        </div>
        <div>
            <label class="text-sm font-medium mb-1.5 block">{{ __t('Корпус') }}</label>
            <input wire:model.live.debounce.500ms="building"
                   type="text"
                   class="field"
                   placeholder="{{ __t('Корп.') }}">
        </div>
        <div>
            <label class="text-sm font-medium mb-1.5 block">{{ __t('Поверх') }}</label>
            <input wire:model.live.debounce.500ms="floor"
                   type="text"
                   class="field"
                   placeholder="{{ __t('Пов.') }}">
        </div>
    </div>
</div>
