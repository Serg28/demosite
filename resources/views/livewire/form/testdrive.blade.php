<div>
    <h2 class="popup-heading">{{$subject}}</h2>
    <form wire:submit="submit" autocomplete="off" id="{{$formId}}">
        @csrf
        @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif

        <input type="text"  @error('name') class="error" @enderror wire:model.live="name" name="name" placeholder="{{__t('Введите Ваше имя')}}">
        @error('name') <p class="error hidden">{{ $message }}</p> @enderror

        <input type="tel" @error('phone') class="error" @enderror wire:model.live="phone" name="phone" placeholder="{{__t('Введите ваш телефон')}}">
        @error('phone') <p class="error hidden">{{ $message }}</p> @enderror

        <div class="button-row">
            <button type="submit" class="main-btn main-btn--red" name="submit" value="Submit" wire:loading.class="main-btn-disable" wire:loading.attr="disabled">
                <span wire:loading.class="hidden">{{__t('Відправити заявку')}}</span><span wire:loading > {{__t('Подождите...')}}</span>
            </button>
        </div>
{{--        @if (session()->has('success'))--}}
{{--            {{ session('success') }}--}}
{{--        @endif--}}
        @error('g_recaptcha_response') <p class="error hidden">{{ $message }}</p> @enderror
    </form>
</div>
