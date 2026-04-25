<div>
    <h2 class="popup-heading">{{__t('Введіть будь-ласка код')}}</h2>
    <form wire:submit="submit" autocomplete="off" id="{{$formId}}">
        <p class="text-popup">{{__t('Ми надіслали спеціальний 6-значний код безпеки на ваш мобільний')}}</p>
        @csrf
        @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif

        <input type="code" @error('code') class="error" @enderror wire:model.live="code" name="code" placeholder="{{__t('Код безопасности')}}">
        @error('code') <p class="error hidden">{{ $message }}</p> @enderror

        <div>
            <span>{{__t('Не прибыл код?')}}</span>
            <button class="btn-security-send" onclick="Livewire.dispatch('openModal', { component: 'auth.form.security.send', arguments: {recaptcha : 'false'}})"> {{__t('Отправить заново')}}</button>
        </div>
        <div class="button-row">
            <button type="submit" class="main-btn main-btn--black">
                <span wire:loading.class="spinner" wire:target="submit"></span>
                <span wire:loading.remove wire:target="submit">{{__t('Войти')}}</span>
            </button>
        </div>
    </form>

</div>

