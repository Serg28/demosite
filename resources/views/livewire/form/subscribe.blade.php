<div class="subscription">
    <div class="container">
        <div class="subscription__wrap flex v--center">
            <div class="left">
                <h2 class="fsz-24 fw-600 heading">{{__t('Підписатись на новини')}}</h2>
                <p>{{__t('Дізнавайся про на акції, розпродажі та новини першим!')}}</p>
            </div>
            <div class="right">
                <form wire:submit="subscribe" id="{{$formId}}" autocomplete="off">
                    @csrf
                    @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif
                    <div class="input-row relative">
                        <input type="email" @error('email') class="error" @enderror wire:model.live="email" name="email" placeholder="{{__t('Ваш e-mail')}}">
                        <div class="input-placeholder-clear"><img src="/assets/images/closer.svg" alt=""></div>
                    </div>
                    <button wire:click="subscribe" type="button" class="main-btn blue-big" name="submit" value="Submit" wire:loading.class="main-btn-disable" wire:loading.attr="disabled">
                        <span wire:loading.class="spinner" wire:target="subscribe"></span>
                        <span wire:loading.remove wire:target="subscribe">{{__t('Подписаться')}}</span>
                    </button>
                </form>
                @error('email') <p class="error">{{ __t($message) }}</p> @enderror
                @error('g_recaptcha_response') <p class="error hidden">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
</div>
