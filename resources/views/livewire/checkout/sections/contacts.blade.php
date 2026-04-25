<div class="form-section p-24 br--br-4 bg--white">
    @php
        $firstStepSuccess = $last_name && $first_name && $patronymic && $phone && $email && $city_id ? 1 : 0;
    @endphp
    <p class="form-heading fsz-18 fw-600">{{__t('Ваші данні')}}</p>
    @if (!app('user'))
        <div class="tabs flex v--center mt-24">
            <div class="tab current" data-screen="1">{{__t('Я новий покупець')}}</div>
            <div class="tabbed js-lw-modal" data-subject="{{__t('Я постійний покупець')}}" data-component="auth.form.login">{{__t('Я постійний клієнт')}}</div>
        </div>
    @endif
    <div class="screens mt-24">
        <div class="screen screen-1">
            <div class="form">
                <lebel class="input small">
                    <input @error('last_name') class="error" @enderror wire:model.live.debounce.800ms="last_name" type="text" name="last_name" placeholder=" ">
                    <span>{{__t('Прізвище')}} *</span>
                </lebel>
                <lebel class="input small">
                    <input @error('first_name') class="error" @enderror wire:model.live.debounce.800ms="first_name" type="text" name="first_name" placeholder=" ">
                    <span>{{__t('Ім’я')}} *</span>
                </lebel>
                <lebel class="input small">
                    <input @error('patronymic') class="error" @enderror wire:model.live.debounce.800ms="patronymic" type="text" name="patronymic" placeholder=" ">
                    <span>{{__t('По-батькові')}} *</span>
                </lebel>
                <lebel class="input small">
                    <input  class="vc @error('phone') error @enderror" wire:model.live.debounce.800ms="phone" type="tel" name="phone" x-mask="+99(999) 999-99-99" placeholder="+38(0__) ___-__-__">
                    <!-- <span>Мобільний телефон</span> -->
                </lebel>
                <lebel class="input small">
                    <input @error('email') class="error" @enderror wire:model.live.debounce.800ms="email" type="email" name="email" placeholder=" ">
                    <span>{{__t('Електронна пошта')}} *</span>
                </lebel>
                <lebel class="input small select">
                    <livewire:checkout.select-city model="city_id" placeholder="{{__t('Ваше місто')}}" :defaultValue="$city_id" wire:key="city-id" />
                </lebel>
                <div id="fastCities" class="flex-row city-row flex v--center">
                    <div class="city color--blue fsz-14" wire:click="setCity(2853)" data-city="2853">{{__t('Київ')}}</div>
                    <div class="city color--blue fsz-14" wire:click="setCity(3855)" data-city="3855">{{__t('Львів')}}</div>
                    <div class="city color--blue fsz-14" wire:click="setCity(6311)" data-city="6311">{{__t('Харків')}}</div>
                    <div class="city color--blue fsz-14" wire:click="setCity(804)" data-city="804">{{__t('Дніпро')}}</div>
                    <div class="city color--blue fsz-14" wire:click="setCity(5033)" data-city="5033">{{__t('Полтава')}}</div>
                </div>
                <div class="main-btn blue-small {!! !$this->isStepSuccess(1) ? 'disabled' : ''!!}" @if($this->isStepSuccess(1)) @click="formStep = 2; sendAnaliticCheckout(1)" @else disabled @endif>{{__t('Перейти до доставки')}} </div>
            </div>
        </div>
        {{--
        <div class="screen screen-2">
            <div class="sc-1">
                <lebel class="input small">
                    <input type="text" class="tel-input" placeholder="+38 (_ _ _) _ _ _  _ _  _ _">
                    <!-- <span>Мобільний телефон</span> -->
                </lebel>
                <button class="main-btn blue-small mt-24 get-sc-2" type="button">Надіслати код</button>
            </div>
            <div class="sc-2">
                <div class="back-to-sc-1 color--blue">Змінити телефон</div>
                <div class="input-row input-num-row flex v--center mt-24">
                    <input type="text" placeholder="_" maxlength="1">
                    <input type="text" placeholder="_" maxlength="1">
                    <input type="text" placeholder="_" maxlength="1">
                    <input type="text" placeholder="_" maxlength="1">
                </div>
                <p class="mt-24 color--gray">Якщо SMS не надійшло, повторний код можна запросити через 60 секунд</p>
                <button class="main-btn blue-small mt-24">Увійти</button>
            </div>
        </div>--}}
    </div>
</div>