<div>
    <form wire:submit="submit" autocomplete="off" id="{{$formId}}" class="flex fd--column mt-24">
        <lebel class="input">
            <input type="text" @error('second_name') class="error" @enderror wire:model.live="second_name" name="second_name" placeholder=" ">
            <span>{{__t('Прізвище')}}</span>
            @error('second_name') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="text" @error('name') class="error" @enderror wire:model.live="name" name="name" placeholder=" ">
            <span>{{__t('Ім’я')}}</span>
            @error('name') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="tel" class="tel-input @error('phone') error @enderror" wire:model.live="phone" name="phone" placeholder=" ">
            <span>{{__t('Контактний телефон')}}</span>
            @error('phone') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input select @error('theme') error @enderror">
            <input type="text" @error('theme') class="error" @enderror wire:model.live="theme" name="theme" placeholder=" ">
            <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M18 9L12 15L6 9" stroke="#606272"/></svg></span>
            <span>{{__t('Тема звернення')}}</span>
            <div class="droppdown">
                <div class="dwoppdown-row">6</div>
                <div class="dwoppdown-row">12</div>
                <div class="dwoppdown-row">24</div>
                <div class="dwoppdown-row">36</div>
            </div>
            @error('theme') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <textarea @error('comment') class="error" @enderror wire:model.live="comment" name="comment" id="" placeholder=" "></textarea>
            <span>{{__t('Текст звернення')}}</span>
            @error('comment') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <div class="flex-row">
            @csrf
            @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif
        </div>
        <button type="submit" class="main-btn blue-big" name="submit" value="Submit" wire:loading.class="main-btn-disable" wire:loading.attr="disabled" >
            <span wire:loading.class="spinner" wire:target="submit"></span>
            <span wire:loading.remove wire:target="submit">{{__t('Надіслати запит')}}</span>
        </button>
    </form>
</div>
