<div class="login-popup">
    <h2 class="fsz-24 fw-600 popup-heading">{{__t('Вхід')}}</h2>
    @if(session()->has('errorLogin'))
        <p class="alert-message error">{{session('errorLogin')}}</p>
    @endif
    <form wire:submit="submit" autocomplete="off" id="{{$formId}}" class="mt-12 validation login-form"  wire:loading.class="opacity-50">
        @csrf
        @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif
        <lebel class="input small mt-16">
            <input type="email" class="frm-input required vv-control @error('email') error @enderror" wire:model="email" placeholder=" ">
            <span>Email *</span>
            {{-- @error('email') <p class="error">{{ $message }}</p> @enderror --}}
        </lebel>
        <lebel class="input small mt-16">
            <input type="password"  class="frm-input required vv-control @error('email') error @enderror" wire:model="password" placeholder=" ">
            <span>{{__t('Пароль')}} *</span>
            {{-- @error('password') <p class="error hidden">{{ $message }}</p> @enderror --}}
        </lebel>

        <div class="flex-row" style="margin-top:10px">
            <a class="btn mt-16 color--blue btn-registration js-lw-modal" data-subject="{{__t('Реєстрація')}}" data-component="auth.form.registration">{{__t('Реєстрація')}}</a>
            <a class="btn mt-16 color--blue lost-password js-lw-modal" data-subject="{{__t('Забули пароль?')}}" data-component="auth.form.forgot">{{__t('Забули пароль?')}}</a>
        </div>

        <div class="button-row">
            <button type="submit" class="main-btn blue-big mt-16">
                <span wire:loading.class="spinner" wire:target="submit"></span>
                <span>{{__t('Вхід')}}</span>
            </button>
        </div>
        {{--@error('g_recaptcha_response') <p class="error hidden">{{ $message }}</p> @enderror --}}
        <div class="mt-24">
            @include('livewire.auth.partials.social')
        </div>
    </form>
</div>

