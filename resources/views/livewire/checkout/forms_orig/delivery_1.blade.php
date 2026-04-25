@if($points)
{{--<p class="mast d-flex">{{__t('Пункт самовывоза')}} <span>*</span></p>
<div class="delivery_points_list select-wrap">
    <select name="delivery_pickup_point_id" id="" class="title select2-points" data-placeholder="{{__t('Выберите пункт самовывоза')}}">
        @foreach($points as $point)
            <option value="{{$point['id']}}">{{$point['title']}}
            </option>
        @endforeach
    </select>
</div>--}}

<div wire:key="delivery-form-{{$delivery_id}}" class="radio-block-row radio-block-row-1" x-data="{ open: false }" style="display: block">
    <div class="input-row">
        <div class="left">
            <select wire:model.lazy="delivery_pickup_point_id" id="delivery_pickup_point_id" class="select">
                <option value="" selected disabled>{{__t('Виберіть склад, будь ласка')}}</option>
                @foreach($points as $point)
                    <option value="{{$point['id']}}">{{$point['title']}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <p>{{__t('Хто забере посилку?')}}</p>
    <div class="radio-row">
        <label for="receiver-radio-user-{{$delivery_id}}">
            <input type="radio" id="receiver-radio-user" wire:model.live.fill="receiver" value="user" name="receiver" checked class="radio" @click="open = false">
            <span>{{__t('Я')}}</span>
        </label>
        <label for="receiver-radio-other-user-{{$delivery_id}}">
            <input type="radio" id="receiver-radio-other-user" wire:model.live.change.fill="receiver" value="other"  name="receiver" class="radio" @click="open = true">
            <span>{{__t('Інша людина')}}</span>
        </label>
    </div>
    <div class="hidden-row-radio_" x-show="open" >
        <p>{{__t('Контактні дані отримувача замовлення')}}</p>
        <div class="input-row">
            <div class="left">
                <input type="text" @error('receiver_first_name') class="error" @enderror wire:model.lazy="receiver_first_name" placeholder="{{__t('Ім’я')}}">
            </div>
            <div class="right">
                <input type="text" @error('receiver_last_name') class="error" @enderror wire:model.lazy="receiver_last_name" placeholder="{{__t('Прізвище')}}">
            </div>
        </div>
        <div class="input-row">
            <div class="left">
                <input type="tel" @error('receiver_phone') class="error" @enderror wire:model.lazy="receiver_phone" placeholder="{{__t('Телефон')}}">
            </div>
            <div class="right">
                <input type="mail" @error('receiver_email') class="error" @enderror wire:model.lazy="receiver_email" placeholder="{{__t('Email')}}">
            </div>
        </div>
    </div>
</div>
@endif
