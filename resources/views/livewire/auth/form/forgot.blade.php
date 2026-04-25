<div class="forgot-popup">
    <h2 class="fsz-24 fw-600 popup-heading">{{__t('Забули пароль?')}}</h2>
    @if(session()->has('errorForget'))
        <p class="alert-message error">{{session('errorForget')}}</p>
    @endif
    <form wire:submit="submit" autocomplete="off" id="{{$formId}}"  class="mt-12 validation forget-form"  wire:loading.class="opacity-50">
        <p class="text-popup">{{__t('Введіть ваш email, і ми надішлемо вам інструкцію з відновлення найближчим часом.')}}</p>
        @csrf
        @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif
        <lebel class="input mt-16">
            <input type="email" class="frm-input required vv-control @error('email') error @enderror" wire:model="email" name="mail">
            <span>Email *</span>
            {{-- @error('email') <p class="error hidden">{{ $message }}</p> @enderror --}}
        </lebel>
        <div class="flex-row mt-14">
            <a class="btn mt-18 color--blue btn-registration js-lw-modal" data-subject="{{__t('Войти')}}" data-component="auth.form.login">{{__t('Авторизация')}}</a>
        </div>
        <div class="button-row">
            <button type="submit" class="main-btn blue-big mt-18">
                <span wire:loading.class="spinner" wire:target="submit"></span>
                <span>{{__t('Далі')}}</span>
            </button>
        </div>
    </form>

</div>

