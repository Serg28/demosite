<div class="contacts__bottom-wrap mt-24 p-24">
    <h3 class="fsz-24 fw-600">{{__t("Зворотній зв'язок")}}</h3>
    <form wire:submit="submit" class="flex fd--column mt-24" autocomplete="off" id="{{$formId}}">
        @csrf
        @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif
        <div class="flex-row flex v--center h--between">
            <lebel class="input">
                <input type="text"  @error('second_name') class="error" @enderror wire:model.live="second_name" name="second_name" placeholder="">
                <span>{{__t('Прізвище')}}</span>
                @error('second_name') <p class="error">{{ __t($message) }}</p> @enderror
            </lebel>
            <lebel class="input">
                <input type="text"  @error('name') class="error" @enderror wire:model.live="name" name="name" placeholder=" ">
                <span>{{__t('Імя')}} *</span>
                @error('name') <p class="error">{{ __t($message) }}</p> @enderror
            </lebel>
        </div>
        <lebel class="input">
            <input type="text" @error('email') class="error" @enderror wire:model.live="email" name="mail" placeholder=" ">
            <span>{{__t('Електронна пошта')}} *</span>
            @error('email') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        {{--
        <lebel class="input select @error('theme') error @enderror">
            <input type="text" @error('theme') class="error" @enderror wire:model.live="theme" name="theme"  placeholder=" ">
            <span class="arrow">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M18 9L12 15L6 9" stroke="#606272"/>
                </svg>
            </span>
            <span>Тема звернення</span>
            <div class="droppdown">
                <div class="dwoppdown-row">6</div>
                <div class="dwoppdown-row">12</div>
                <div class="dwoppdown-row">24</div>
                <div class="dwoppdown-row">36</div>
            </div>
            @error('theme') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        --}}
        <lebel class="input">
            <textarea @error('comment') class="error" @enderror wire:model.live="comment" name="comment" id="" placeholder=""></textarea>
            <span>{{__t('Коментар')}} *</span>
            @error('comment') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
{{--        <div class="checkbox-row">--}}
{{--            <label for="input-checkbox">--}}
{{--                <input type="checkbox" class="checkbox @error('checkbox') error @enderror " wire:model.live="checkbox" value="1" name="checkbox" id="input-checkbox" >--}}
{{--                <p>{{__t('Я подтверждаю, что прочитал и одобрил')}} <a href="{{getUrl('privacy-policy')}}">{{__t('політику конфіденційності')}}</a></p>--}}
{{--            </label>--}}
{{--            --}}{{-- @error('checkbox') <p class="error">{{ __t($message) }}</p> @enderror  --}}
{{--        </div>--}}
        <button type="submit" class="main-btn blue-small" name="submit" value="Submit" wire:loading.class="main-btn-disable" wire:loading.attr="disabled"  wire:target="submit">
            <span wire:loading.class="spinner" wire:target="submit"></span>
            <span wire:target="submit">{{__t('Відправити')}}</span>
        </button>
    </form>
</div>


