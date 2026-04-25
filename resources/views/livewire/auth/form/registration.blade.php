<div class="registration-popup">
    <h2 class="fsz-24 fw-600 popup-heading">{{__t('Реєстрація')}}</h2>
    @if(session()->has('errorReg'))
        <p class="alert-message error">{{session('errorReg')}}</p>
    @endif
    <form wire:submit="submit" autocomplete="off" id="{{$formId}}"  class="mt-12 validation registration-form" wire:loading.class="opacity-50">

        @csrf
        @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif
        <lebel class="input small mt-16">
            <input type="text" class="frm-input required vv-control @error('last_name') error @enderror" wire:model.blur="last_name" placeholder=" ">
            <span>{{__t('Прізвище')}}</span>
            {{--@error('last_name') <p class="error hidden">{{ $message }}</p> @enderror  --}}
        </lebel>
        <lebel class="input small mt-16">
            <input type="text" class="frm-input required vv-control @error('first_name') error @enderror" wire:model.blur="first_name" placeholder=" ">
            <span>{{__t("Ім'я")}}</span>
            {{-- @error('first_name') <p class="error hidden">{{ $message }}</p> @enderror --}}
        </lebel>
        {{--<lebel class="input mt-16">
            <input type="text" class="frm-input required vv-control @error('patronymic') error @enderror" wire:model.live="patronymic" name="patronymic" placeholder="{{__t('Отчество')}}">
            @error('patronymic') <p class="error hidden">{{ $message }}</p> @enderror
        </lebel> --}}
        <lebel class="input small mt-16">
            <input type="text" class="frm-input @error('phone') error @enderror" x-mask="+99(999) 999-99-99" wire:model.debounce="phone" x-mask="+99(999) 999-99-99" placeholder=" ">
            <span>{{__t("Телефон")}}</span>
            {{-- @error('phone') <p class="error hidden">{{ $message }}</p> @enderror --}}
        </lebel>
        <lebel class="input small mt-16">
            <input type="email" class="frm-input required vv-control @error('email') error @enderror" wire:model.blur="email" placeholder=" ">
            <span>Email</span>
            {{-- @error('email') <p class="error hidden">{{ $message }}</p> @enderror  --}}
        </lebel>
        <lebel class="input small mt-16">
            <input type="password" class="frm-input required vv-control @error('password') error @enderror" wire:model.blur="password" placeholder=" ">
            <span>{{__t('Пароль')}}</span>
            {{-- @error('password') <p class="error hidden">{{ $message }}</p> @enderror --}}
        </lebel>
        <lebel class="input small mt-16">
            <input type="password" class="frm-input required vv-control @error('re_password') error @enderror" wire:model.blur="re_password" placeholder=" ">
            <span>{{__t('Підтвердити пароль')}}</span>
            {{-- @error('re_password') <p class="error hidden">{{ $message }}</p> @enderror --}}
        </lebel>

        <div class="checkbox-row mt-10 @error('checkbox') error @enderror">
            <label for="input-checkbox">
                <input type="checkbox" class="checkbox" wire:model.change="checkbox" id="input-checkbox">
                <p>{{__t('Я погоджуюся з')}} <a href="{{getUrl('publicna-oferta')}}" target="_blank" class="color--blue">{{__t('умовами та політикою')}}</a></p>
            </label>
            {{-- @error('checkbox') <p class="error hidden">{{ $message }}</p> @enderror --}}
        </div>

        <div class="flex-row" style="margin-top:10px">
            <a class="btn mt-16 color--blue btn-registration js-lw-modal" data-subject="{{__t('Войти')}}" data-component="auth.form.login">{{__t('Авторизация')}}</a>
        </div>
        <div class="button-row">
            <button type="submit" class="main-btn blue-big mt-24">
                <span wire:loading.class="spinner" wire:target="submit"></span>
                <span>{{__t('Реєстрація')}}</span>
            </button>
        </div>

        <div class="mt-24">
            @include('livewire.auth.partials.social')
        </div>
    </form>

</div>

