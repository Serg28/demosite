{{--<p class="mast d-flex">{{__t('Адрес')}} <span>*</span></p>
<input type="text" name="address" class="title" placeholder="{{__t('Введите адрес доставки')}}" autocomplete="off" minlength="2">
--}}


<div wire:key="delivery-form-{{$delivery_id}}" class="radio-block-row radio-block-row-3" style="display: block">
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
