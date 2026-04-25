<div>
    <h2 class="popup-heading">{{$subject}}</h2>
    <form wire:submit="submit" autocomplete="off" id="{{$formId}}">
        <p class="text-popup">{{__t('Вкажіть ваш номер телефону та ім’я. Ми зателефонуємо вам найближчим часом.')}}</p>
        @csrf
        @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif

        <input type="text"  @error('name') class="error" @enderror wire:model.live="name" name="name" placeholder="{{__t('Введите Ваше имя')}}">
        @error('name') <p class="error">{{ $message }}</p> @enderror

        <input type="tel" @error('phone') class="error" @enderror wire:model.live="phone" name="phone" placeholder="{{__t('Введите ваш телефон')}}">
        @error('phone') <p class="error">{{ $message }}</p> @enderror

        <div class="button-row">
            <button type="submit" class="main-btn main-btn--red" name="submit" value="Submit" wire:loading.class="main-btn-disable" wire:loading.attr="disabled"  wire:target="submit">
                <span wire:loading.class="spinner" wire:target="submit"></span>
                <span wire:loading.remove wire:target="submit">{{__t('Отправить')}}</span>
            </button>
        </div>

        {{--@error('g_recaptcha_response') <p class="error hidden">{{ $message }}</p> @enderror --}}
    </form>
</div>
