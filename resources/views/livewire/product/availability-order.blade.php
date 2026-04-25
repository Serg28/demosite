<div>
    <h3 class="fsz-24 fw-600 popup-heading">{{__t('Повідомте мене про появу товару')}}</h3>
    <form wire:submit="submit" id="{{$formId}}" wire:loading.class="disabled opacity-50" class="mt-12">
        @csrf
        @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif
        <input type="hidden"  wire:model="product_id" >
        <lebel class="input mt-16">
            <input @error('name') class="error" @enderror wire:model.debounce.700ms.live="name" placeholder=" ">
            <span>{{__t("Ім'я")}} *</span>
        </lebel>

        <lebel class="input mt-16">
            <input @error('phone') class="error" @enderror wire:model.debounce.700ms.live="phone" type="tel" name="phone" x-mask="+99(999) 999-99-99" placeholder=" ">
            <span>{{__t('Телефон')}} *</span>
        </lebel>

        <lebel class="input mt-16">
            <input @error('email') class="error" @enderror wire:model.debounce.700ms.live="email" placeholder=" ">
            <span>{{__t("Email")}} *</span>
        </lebel>

        {{--
        <label for="input-agree-1" class="checkbox flex v--start mt-24">
            <input type="checkbox" id="input-agree-1">
            <p class="fsz-14">{{__t('Я погоджуюсь, з тим, що ціна товару на момент появи в наявності може відрізнятися від ціни, яка вказана на даний момент')}}</p>
        </label>--}}
        <button wire:click="submit" class="main-btn blue-big mt-24" wire:loading.class="disabled" wire:loading.attr="disabled">
            <span wire:loading.class="spinner" wire:target="submit"></span>
            {{__t('Відправити')}}
        </button>

        <span class="fsz-12 mt-24 text--center flex">{!! str_replace('[url]', geturl('publicna-oferta'), __t('Натискаючи на кнопку “Надіслати” ви погоджуєтеся з <a href="[url]" target="_blank" class="color--blue ml-4">угодою користувача</a>')) !!}</span>
    </form>
</div>