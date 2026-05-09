<div class="mt-4 space-y-3">
    {{-- ── Пошук міста (НП, Укрпошта, Justin, Meest, Rozetka) ─────── --}}
    @if($this->hasWarehouseSearch)
        <div x-data="{ open: false }" class="relative">
            <label class="text-sm font-medium mb-1.5 block">{{ __t('Місто') }}</label>
            <div class="relative">
                <input
                    wire:model.live.debounce.300ms="citySearch"
                    @focus="open = true"
                    @click.outside="open = false"
                    type="text"
                    class="field pr-8"
                    placeholder="{{ __t('Введіть назву міста...') }}"
                >
                @if($selectedCityId !== null)
                    <button wire:click="clearCity"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>
            @if($this->citySuggestions->isNotEmpty())
                <div x-show="open"
                     class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto">
                    @foreach($this->citySuggestions as $city)
                        <button wire:click="selectCity({{ $city->id }}, '{{ addslashes($city->t('title')) }}')"
                                @click="open = false"
                                class="w-full text-left px-4 py-2.5 text-sm hover:bg-brand-light transition-colors">
                            {{ $city->t('title') }}
                        </button>
                    @endforeach
                </div>
            @elseif(mb_strlen($citySearch) >= 2 && $selectedCityId === null)
                <p class="text-xs text-ink-muted mt-1">{{ __t('Нічого не знайдено') }}</p>
            @endif
        </div>

        {{-- ── Пошук відділення / поштомату ─────────────────────────── --}}
        @if($selectedCityId !== null)
            <div x-data="{ open: false }" class="relative">
                <label class="text-sm font-medium mb-1.5 block">{{ __t($this->warehouseLabel) }}</label>
                <div class="relative">
                    <input
                        wire:model.live.debounce.300ms="warehouseSearch"
                        @focus="open = true"
                        @click.outside="open = false"
                        type="text"
                        class="field pr-8"
                        placeholder="{{ __t('Введіть номер або адресу...') }}"
                    >
                    @if($selectedWarehouseId !== null)
                        <button wire:click="clearWarehouse"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>
                @if($this->warehouseSuggestions->isNotEmpty())
                    <div x-show="open"
                         class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                        @foreach($this->warehouseSuggestions as $warehouse)
                            <button wire:click="selectWarehouse({{ $warehouse->id }}, '{{ addslashes($warehouse->t('title')) }}')"
                                    @click="open = false"
                                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-brand-light transition-colors">
                                {{ $warehouse->t('title') }}
                            </button>
                        @endforeach
                    </div>
                @elseif(mb_strlen($warehouseSearch) >= 1 && $selectedWarehouseId === null)
                    <p class="text-xs text-ink-muted mt-1">
                        {{ __t('Нічого не знайдено — базу відділень буде завантажено пізніше') }}
                    </p>
                @endif
            </div>
        @endif
    @endif

    {{-- ── Кур'єр: адреса ────────────────────────────────────────────── --}}
    @if($this->isCourierDelivery)
        <div>
            <label class="text-sm font-medium mb-1.5 block">{{ __t('Адреса доставки') }}</label>
            <input wire:model.live.debounce.500ms="address"
                   type="text"
                   class="field"
                   placeholder="{{ __t('Вулиця, будинок, квартира') }}">
        </div>
    @endif

    {{-- ── Самовивіз: список пунктів ──────────────────────────────────── --}}
    @if($this->isPickupDelivery)
        @if($this->pickupPoints->isEmpty())
            <p class="text-sm text-ink-muted">{{ __t('Пункти самовивозу тимчасово недоступні') }}</p>
        @else
            <div class="space-y-2">
                <label class="text-sm font-medium mb-1.5 block">{{ __t('Оберіть пункт видачі') }}</label>
                @foreach($this->pickupPoints as $point)
                    <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition-colors
                        {{ $pickupPointId === $point->id ? 'border-brand bg-brand-light' : 'border-gray-100 hover:border-gray-200' }}">
                        <input type="radio"
                               wire:click="selectPickupPoint({{ $point->id }})"
                               class="accent-brand"
                               {{ $pickupPointId === $point->id ? 'checked' : '' }}>
                        <div>
                            <p class="text-sm font-medium">{{ $point->t('title') }}</p>
                            @if($point->address)
                                <p class="text-xs text-ink-muted">{{ $point->address }}</p>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
        @endif
    @endif
</div>
