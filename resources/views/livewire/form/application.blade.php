<div>
    <h2 class="popup-heading">{{$subject}}</h2>
    <form wire:submit="submit" autocomplete="off" id="{{$formId}}">
        @csrf
        @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif

        <input type="text"  @error('name') class="error" @enderror wire:model.live="name" name="name" placeholder="{{__t('Введите Ваше имя')}}">
        @error('name') <p class="error">{{ $message }}</p> @enderror

        <input type="tel" @error('phone') class="error" @enderror wire:model.live="phone" name="phone" placeholder="{{__t('Введите ваш телефон')}}">
        @error('phone') <p class="error">{{ $message }}</p> @enderror

        <input type="email" @error('email') class="error" @enderror wire:model.live="email" name="mail" placeholder="{{__t('Вашу электронную почту')}}">
        @error('email') <p class="error">{{ $message }}</p> @enderror

        <textarea @error('comment') class="error" @enderror wire:model.live="comment" name="comment" id="" placeholder="{{__t('Ваше сообщение')}}"></textarea>
        @error('comment') <p class="error">{{ $message }}</p> @enderror
        <div class="button-row">
            <button type="submit" class="main-btn main-btn--red-transparent" name="submit" value="Submit" wire:loading.class="main-btn-disable" wire:loading.attr="disabled">
                <span wire:loading.class="spinner" wire:target="submit"></span>
                <span wire:loading.remove wire:target="submit">{{__t('Отправить')}}</span>
            </button>
        </div>
    </form>
    @error('g_recaptcha_response') <p class="error">{{ $message }}</p> @enderror
</div>
