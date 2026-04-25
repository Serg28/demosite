<div wire:key="delivery-form-{{$delivery_id}}" class="radio-block-row radio-block-row-2" style="display: block">
    <div class="input-row">
        <div class="right">
            {{-- Выбор отделения --}}
            @if(!empty($city_id))
                <livewire:checkout.select-np-warehouses wire:model.live="np_warehouse_id" model="np_warehouse_id" placeholder="{{__t('Відділення «Нова Пошта» *')}}" :cityId="$city_id" _lazy />
            @endif
            {{-- / Выбор отделения --}}
     </div>
    </div>

    <p>{{__t('Контактні дані отримувача замовлення')}}</p>
    <div class="input-row">
        <div class="left">
            <input type="text" @error('receiver_first_name') class="error" @enderror wire:model.lazy="receiver_first_name" name="receiver_first_name" placeholder="{{__t('Ім’я')}}">
        </div>
        <div class="right">
            <input type="text" @error('receiver_last_name') class="error" @enderror wire:model.lazy="receiver_last_name" name="receiver_last_name" placeholder="{{__t('Прізвище')}}">
        </div>
    </div>
    <div class="input-row">
        <div class="left">
            <input type="tel" @error('receiver_phone') class="error" @enderror wire:model.lazy="receiver_phone" name="receiver_phone" placeholder="{{__t('Телефон')}}">
        </div>
        <div class="right">
            <input type="mail" @error('receiver_email') class="error" @enderror wire:model.lazy="receiver_email" name="receiver_email" placeholder="{{__t('Email')}}">
        </div>
    </div>
</div>

{{--@endif --}}
