<div>
    <h2 class="popup-heading">{!! __t('Ви точно хочете<br> видалити акаунт?') !!}</h2>
    <form wire:submit="submit" autocomplete="off" id="{{$formId}}">
        @csrf
        @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif

        <input type="email" @error('email') class="error" @enderror wire:model.live="email" name="mail" placeholder="{{__t('Ваша электронная почта')}}">
        @error('email') <p class="error hidden">{{ $message }}</p> @enderror

        <div class="btn-login">{{__t('назад')}}</div>

        <div class="button-row">
            <button type="submit" class="main-btn main-btn--black">
                <span wire:loading.class="spinner" wire:target="submit"></span>
                <span wire:loading.remove wire:target="submit">{{__t('Далі')}}</span>
            </button>
        </div>
    </form>
</div>

