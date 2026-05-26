<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-bold text-xl">{{ __t('Мої адреси') }}</h2>
        @if(!$showForm)
            <button wire:click="$set('showForm', true)" class="btn btn-p btn-sm">
                + {{ __t('Додати адресу') }}
            </button>
        @endif
    </div>

    {{-- ── Add form ──────────────────────────────────────────────────── --}}
    @if($showForm)
        <div class="card p-5 mb-4">
            <h3 class="font-bold text-base mb-4">{{ __t('Нова адреса') }}</h3>
            <div class="space-y-4">

                {{-- Label --}}
                <div>
                    <label class="text-sm font-medium mb-1.5 block text-ink-muted">
                        {{ __t('Назва (Дім, Робота, ...)') }}
                    </label>
                    <input type="text" wire:model="form.label" class="field"
                           placeholder="{{ __t('Необов\'язково') }}">
                    @error('form.label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Phone --}}
                <x-phone-input wire:model="form.phone" :label="__t('Телефон')" />

                {{-- City autocomplete --}}
                <x-checkout.autocomplete
                    :label="__t('Місто') . ' *'"
                    :placeholder="__t('Введіть назву міста...')"
                    :search-url="route('api.v1.checkout.cities')"
                    :selected-id="$cityId"
                    :selected-title="$cityTitle"
                    select-method="selectCity"
                    clear-method="clearCity"
                    :min-chars="2"
                />
                @error('cityId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                {{-- Delivery method (з'являється після вибору міста) --}}
                @if($cityId !== null)
                    <div>
                        <label class="text-sm font-medium mb-1.5 block">
                            {{ __t('Спосіб доставки') }} <span class="text-red-500">*</span>
                        </label>
                        @if($this->deliveries->isEmpty())
                            <p class="text-xs text-ink-muted">{{ __t('Немає доступних методів доставки для цього міста') }}</p>
                        @else
                            <div class="space-y-1.5">
                                @foreach($this->deliveries as $delivery)
                                    <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-lg border transition-colors
                                                  {{ $deliveryId === $delivery->id ? 'border-brand bg-brand-light' : 'border-gray-200 hover:border-gray-300' }}">
                                        <input type="radio"
                                               name="delivery_id"
                                               wire:click="selectDelivery({{ $delivery->id }}, '{{ $delivery->slug }}')"
                                               class="accent-brand"
                                               {{ $deliveryId === $delivery->id ? 'checked' : '' }}>
                                        <span class="text-sm font-medium">{{ $delivery->t('title') }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('deliveryId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- Warehouse autocomplete (для не-адресних методів) --}}
                @if($deliveryId !== null && !$this->isAddressDelivery)
                    <x-checkout.autocomplete
                        :label="$this->warehouseLabel . ' *'"
                        :placeholder="__t('Введіть номер або адресу...')"
                        :search-url="$this->warehouseSearchUrl"
                        :selected-id="$warehouseId"
                        :selected-title="$warehouseTitle"
                        select-method="selectWarehouse"
                        clear-method="clearWarehouse"
                        :min-chars="1"
                    />
                    @error('warehouseId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @endif

                {{-- Address fields (courier/np_address) --}}
                @if($deliveryId !== null && $this->isAddressDelivery)
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="text-sm font-medium mb-1.5 block">
                                {{ __t('Вулиця') }} <span class="text-red-500">*</span>
                            </label>
                            <input wire:model.live.debounce.500ms="street" type="text" class="field"
                                   placeholder="{{ __t('Назва вулиці') }}">
                            @error('street') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium mb-1.5 block">
                                {{ __t('Будинок') }} <span class="text-red-500">*</span>
                            </label>
                            <input wire:model.live.debounce.500ms="house" type="text" class="field"
                                   placeholder="{{ __t('Номер') }}">
                            @error('house') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-1.5 block">{{ __t('Квартира') }}</label>
                        <input wire:model.live.debounce.500ms="apartment" type="text" class="field w-32"
                               placeholder="{{ __t('Кв.') }}">
                    </div>
                @endif

                {{-- Primary checkbox --}}
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" wire:model="form.is_primary" class="rounded text-brand accent-brand">
                    {{ __t('Зробити основною') }}
                </label>

                <div class="flex gap-2 pt-1">
                    <button wire:click="add" class="btn btn-p flex-1">{{ __t('Зберегти') }}</button>
                    <button wire:click="cancelForm" class="btn btn-o flex-1">{{ __t('Скасувати') }}</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── List ──────────────────────────────────────────────────────── --}}
    @if($this->addresses->isEmpty())
        <div class="card p-10 text-center text-ink-muted">
            <p>{{ __t('Адрес поки немає') }}</p>
        </div>
    @else
        <div class="grid sm:grid-cols-2 gap-3">
            @foreach($this->addresses as $address)
                <div class="card p-5 {{ $address->is_primary ? 'ring-2 ring-brand' : '' }}"
                     wire:key="addr-{{ $address->id }}">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($address->label)
                                <p class="font-bold text-sm">{{ $address->label }}</p>
                            @endif
                            @if($address->is_primary)
                                <span class="badge bdg-new text-[10px]">{{ __t('Основна') }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if(!$address->is_primary)
                                <button wire:click="setPrimary({{ $address->id }})"
                                        class="text-xs text-brand hover:underline whitespace-nowrap">
                                    {{ __t('Основна') }}
                                </button>
                            @endif
                            <button wire:click="delete({{ $address->id }})"
                                    wire:confirm="{{ __t('Видалити адресу?') }}"
                                    class="text-gray-400 hover:text-red-500 text-xl leading-none transition-colors">
                                ×
                            </button>
                        </div>
                    </div>
                    @if($address->phone)
                        <p class="text-sm text-ink-muted mb-1">{{ $address->phone }}</p>
                    @endif
                    @if($address->displayAddress())
                        <p class="text-sm text-ink-muted leading-snug">{{ $address->displayAddress() }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
