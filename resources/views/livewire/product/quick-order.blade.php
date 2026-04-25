<form wire:submit="submit" class="quick-purchase  mt-40 p-16" autocomplete="off" id="{{$formId}}" wire:loading.class="disabled opacity-50" >

    @csrf
    @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif

    <p class="fw-600">{{__t('Швидка покупка')}}</p>
    <div class="flex-row flex v--center mt-16">
        <input type="tel" wire:model="phone" class="tel-input_ @error('phone') error @enderror" x-mask="+99(999) 999-99-99" placeholder="+38(0__) ___-__-__">
        <button type="submit" class="main-btn blue-big ml-12" wire:loading.class="main-btn-disable" wire:loading.attr="disabled">
            <span wire:loading.class="spinner" wire:target="submit"></span>
            {{__t('Купити в 1 клік')}}
        </button>
    </div>
    {{--
    @error('phone') <p class="error">{{ $message }}</p> @enderror
    @error('g_recaptcha_response') <p class="error">{{ $message }}</p> @enderror
    --}}
</form>