{{-- Наш курьев адресная --}}
<div wire:key="delivery-form-{{$delivery_id}}" class="droppdown-wrap mt-24" style="display: block" x-data="{receive_open: false}">
    @if(!empty($city_id))

    {{-- Выбор улицы --}}
    <div  x-bind:style="{display: receive_open ? 'none' : 'block'}">
        <livewire:checkout.select-np-street wire:model.live.debounce.800ms="street" model="street" placeholder="{{__t('Вулиця *')}}" :cityId="$city_id" _lazy />
    </div>
    {{-- / Выбор улицы --}}

    {{-- Адрес --}}
    <div class="checkbox-row">
        <label for="other_street" class="checkbox flex v--center mt-24">
            <input id="other_street" type="checkbox" wire:model.live="other_street" value="1" class="other-cust" @click="receive_open = $event.target.checked ? true : false">
            <span>{{__t('Немає потрібної вулиці в списку')}}</span>
        </label>
        <div class="droppdown-other" x-bind:style="{display: receive_open ? 'block' : 'none'}">
            <div class="wrapper">
                <div class="input small">
                    <input wire:model="address" placeholder=" ">
                    <span>{{__t('Напишіть назву вулиці')}} *</span>
                </div>
            </div>
        </div>
    </div>

    <div class="droppdown-other">
        <div class="wrapper">
            <div class="input small">
                <input wire:model.lazy.debounce.800px="house" placeholder=" ">
                <span>{{__t('Будинок')}} *</span>
            </div>
            <div class="input small">
                <input wire:model="apartment" placeholder=" ">
                <span>{{__t('Квартира')}}</span>
            </div>
            <div class="input small">
                <input wire:model="building" placeholder=" ">
                <span>{{__t('Корпус')}}</span>
            </div>
            <div class="input small not-full-width">
                <input wire:model="floor" placeholder=" ">
                <span>{{__t('Поверх')}}</span>
            </div>
            <label for="is_elevator" class="checkbox flex v--center">
                <input id="is_elevator" type="checkbox" wire:model="is_elevator" value="1" class="other-cust">
                <span>{{__t('Є ліфт')}}</span>
            </label>
            <label for="is_lifting" class="checkbox flex v--center">
                <input id="is_lifting" type="checkbox" wire:model="is_lifting" value="1" class="other-cust">
                <span>{{__t('Підняття на поверх')}}</span>
            </label>
        </div>
    </div>
    {{-- /Адресс --}}

    @endif

</div>

{{--@endif --}}
