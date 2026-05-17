<div class="mt-4 space-y-3">
    {{-- Відділення / поштомат — Alpine autocomplete → API --}}
    @if($this->hasWarehouseSearch && $cityId !== null)
        @php
            $warehouseUrl = route('api.v1.checkout.warehouses') . '?delivery_slug=' . $deliverySlug . '&city_id=' . $cityId;
        @endphp
        <x-checkout.autocomplete
            :label="__t($this->warehouseLabel)"
            :placeholder="__t('Введіть номер або адресу...')"
            :search-url="$warehouseUrl"
            :selected-id="$selectedWarehouseId"
            :selected-title="$selectedWarehouseTitle"
            select-method="selectWarehouse"
            clear-method="clearWarehouse"
            :min-chars="1"
        />
    @endif

    {{-- Кур'єр: адреса --}}
    @if($this->isCourierDelivery)
        <div>
            <label class="text-sm font-medium mb-1.5 block">{{ __t('Адреса доставки') }}</label>
            <input wire:model.live.debounce.500ms="address"
                   type="text"
                   class="field"
                   placeholder="{{ __t('Вулиця, будинок, квартира') }}">
        </div>
    @endif

    {{-- Самовивіз: список пунктів --}}
    @if($this->isPickupDelivery)
        @if($this->pickupPoints->isEmpty())
            <p class="text-sm text-ink-muted">{{ __t('Пункти самовивозу тимчасово недоступні') }}</p>
        @else
            <div class="space-y-2">
                <label class="text-sm font-medium mb-1.5 block">{{ __t('Оберіть пункт видачі') }}</label>
                @foreach($this->pickupPoints as $point)
                    <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition-colors
                        {{ $pickupPointId === $point->id ? 'border-brand bg-brand-light' : 'border-gray-100 hover:border-gray-200' }}"
                        wire:key="pickup-{{ $point->id }}">
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
