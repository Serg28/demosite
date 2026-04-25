{{--@if($points) --}}
{{--<p class="mast d-flex">{{__t('Отделение')}} <span>*</span></p>
<div class="delivery_points_list select-wrap">
    <select name="np_warehouse_id" id="" class="title select2-points" data-placeholder="{{__t('Выберите отделение')}}">
        <option value="" selected disabled>{{__t('Выберите отделение')}}
        </option>
        @foreach($points as $point)
            <option value="{{$point['id']}}">{{$point['title']}}
            </option>
        @endforeach
    </select>
</div> --}}
{{--{{dd($checkoutErrors['city_id'][0])}} --}}
<div wire:key="delivery-form-{{$delivery_id}}" class="radio-block-row radio-block-row-2" style="display: block">
    <div class="input-row">
        {{-- Выбор города --}}
        <div class="left">
            <livewire:checkout.select-city model="city_id" placeholder="{{__t('Оберіть місто *')}}" :defaultValue="$city_id" _lazy />
        </div>
        {{-- / Выбор города --}}

        <div class="right">
            {{-- Выбор отделения --}}
            @if(!empty($city_id))
                <livewire:checkout.select-np-warehouses model="np_warehouse_id" placeholder="{{__t('Відділення «Нова Пошта» *')}}" defaultValue="" :cityId="$city_id" _lazy />
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
