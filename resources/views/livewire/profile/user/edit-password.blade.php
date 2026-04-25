<div>
<h3 class="fsz-24 fw-600 popup-heading">{{__t('Зміна пароля')}}</h3>
            <div class="sc-1">

                @if(session()->has('error_password'))
                    <p class="alert-message error">{{session('error_password')}}</p>
                @endif

                <form wire:submit="submit" action="" id="{{$formId}}" autocomplete="off" class="mt-12" wire:loading.class="opacity-50" wire:target="submit">
                    @csrf
                    @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif
                    <lebel class="input mt-16">
                        <input type="password" wire:model="old_password" placeholder=" " class=" @error('old_password') error @enderror ">
                        <span>{{__t('Поточний пароль')}} *</span>
                    </lebel>
                    <lebel class="input mt-16">
                        <input type="password" wire:model="new_password" placeholder=" " class=" @error('new_password') error @enderror ">
                        <span>{{__t('Новий пароль')}} *</span>
                    </lebel>
                    <lebel class="input mt-16">
                        <input type="password" wire:model="re_password" placeholder=" " class=" @error('re_password') error @enderror ">
                        <span>{{__t('Повторіть новий пароль')}} *</span>
                    </lebel>
                    <div class="button-row mt-24 flex v--center">
                        <button class="main-btn border-big" type="button" wire:click="$dispatch('closeModal')">{{__t('Скасувати')}}</button>
                        <button class="main-btn blue-big" type="submit">{{__t('Зберегти')}}</button>
                    </div>
                </form>
            </div>
</div>