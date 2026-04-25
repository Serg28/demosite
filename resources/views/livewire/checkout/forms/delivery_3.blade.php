{{-- Новая почта отделения --}}
<div wire:key="delivery-form-{{$delivery_id}}" class="droppdown-wrap mt-24" style="display: block">

    {{-- Выбор отделения --}}
    @if(!empty($city_id))
        <livewire:checkout.select-np-warehouses wire:model.live="np_warehouse_id" model="np_warehouse_id" placeholder="{{__t('Відділення «Нова Пошта» *')}}" :cityId="$city_id" _lazy />
    @endif
    {{-- / Выбор отделения --}}

    {{--
    <lebel class="input small select">
        <input type="text" placeholder=" ">
        <div class="arrow">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M18 9L12 15L6 9" stroke="#606272"/>
            </svg>
        </div>
        <span>Відділення №5: вул. Федорова, 32 (м. Олімпійс...</span>
        <div class="droppdown">
            <div class="dwoppdown-row">Кіїв</div>
            <div class="dwoppdown-row">Львів</div>
            <div class="dwoppdown-row">Харків</div>
            <div class="dwoppdown-row">Дніпро</div>
            <div class="dwoppdown-row">Полтава</div>
        </div>
    </lebel>--}}

    {{--
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
    --}}
</div>

{{--@endif --}}
