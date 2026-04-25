<div>
<h3 class="fsz-24 fw-600 popup-heading">{{__t('Редагування')}}</h3>
            <div class="sc-1">

                @if(session()->has('error'))
                    <p class="alert-message error">{{session('error')}}</p>
                @endif

                <form wire:submit="submit" action="" id="{{$formId}}" autocomplete="off" class="mt-12" wire:loading.class="opacity-50" wire:target="submit">
                    @csrf
                    @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif
                    <lebel class="input mt-16">
                        <input type="text" wire:model="last_name" placeholder=" " class=" @error('last_name') error @enderror ">
                        <span>{{__t('Прізвище')}} *</span>
                    </lebel>
                    <lebel class="input mt-16">
                        <input type="text" wire:model="first_name" placeholder=" " class=" @error('first_name') error @enderror ">
                        <span>{{__t('Ім\'я')}} *</span>
                    </lebel>
                    <lebel class="input mt-16">
                        <input type="text" wire:model="patronymic" placeholder=" " class=" @error('patronymic') error @enderror ">
                        <span>{{__t('По-батькові')}} *</span>
                    </lebel>
                    <lebel class="input vc mt-16">
                        <input type="tel" wire:model="phone" class="@error('phone') error @enderror vc" x-mask="+99(999) 999-99-99" placeholder="+38(0__) ___-__-__">
                    </lebel>
                    <lebel class="input mt-16">
                        <input type="email" @if(!empty($email)) disabled readonly @endif placeholder=" " value="{{$email}}" class=" @error('patronymic') error @enderror ">
                        <span>Email</span>
                    </lebel>
                    <div class="button-row mt-24 flex v--center">
                        <button class="main-btn border-big" type="button" wire:click="$dispatch('closeModal')">{{__t('Скасувати')}}</button>
                        <button class="main-btn blue-big" type="submit">{{__t('Зберегти')}}</button>
                    </div>
                </form>
            </div>
            {{--
            <div class="sc-2">
                <div class="wrap mt-12 flex fd--column">
                    <p>Код з SMS, надіслано на +38 (099) 310 09 90</p>
                    <form action="" autocomplete="off">
                        <div class="input-row input-num-row flex v--center">
                            <input type="text" placeholder="_" maxlength="1">
                            <input type="text" placeholder="_" maxlength="1">
                            <input type="text" placeholder="_" maxlength="1">
                            <input type="text" placeholder="_" maxlength="1">
                        </div>
                    </form>
                    <p class="color--gray">Якщо SMS не надійшло, повторний код можна запросити через 60 секунд</p>
                    <div class="change-num color--blue">Змінити номер телефону</div>
                    <button class="main-btn blue-big">Зберегти</button>
                </div>
            </div> --}}
</div>