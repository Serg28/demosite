<div>
    <p class="fsz-18 fw-600">{{$subject}}</p>
    <span class="mt-16 flex pb-24">{{__t('Заповнюється для кожної одиниці товару, що передається Клієнтом в СЦК транспортом Компанії або з використанням поштових служб.')}}</span>
    <form wire:submit="submit" autocomplete="off" id="{{$formId}}" class="pt-24 flex fd--column" x-data="{}">
        <lebel class="input select @error('statement') error @enderror">
            <input type="text" wire:model.live="statement" placeholder=" " @error('statement') class="error" @enderror>
            <span class="arrow">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M18 9L12 15L6 9" stroke="#606272"/></svg>
            </span>
            <span>{{__t('Оберіть вид заяви')}} *</span>
            <div  id="statement" class="droppdown @error('statement') error @enderror">
                <div class="dwoppdown-row" x-on:click="$wire.set('statement','{{__t('Гарантійне обслуговування')}}')">{{__t('Гарантійне обслуговування')}}</div>
                <div class="dwoppdown-row" x-on:click="$wire.set('statement','{{__t('Обмін/повернення товару')}}')">{{__t('Обмін/повернення товару')}}</div>
                <div class="dwoppdown-row" x-on:click="$wire.set('statement','{{__t('Співпраця')}}')">{{__t('Співпраця')}}</div>
            </div>
            @error('statement') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="text" @error('pib') class="error" @enderror wire:model.debounce.800ms.change="pib" name="pib" placeholder=" ">
            <span>{{__t('ПІБ')}} *</span>
            @error('pib') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="tel" class="tel-input_ vc @error('phone') error @enderror" wire:model.debounce.800ms.change="phone" name="phone" x-mask="+99(999) 999-99-99" placeholder="+38 (_ _ _) _ _ _  _ _  _ _">
            @error('phone') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="text" @error('order_nom') class="error" @enderror wire:model="order_nom" name="order_nom" placeholder=" ">
            <span>{{__t('№ замовлення')}}</span>
            @error('order_nom') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="text" @error('product_name') class="error" @enderror wire:model="product_name" name="product_name" placeholder=" ">
            <span>{{__t('Назва товару')}}</span>
            @error('product_name') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="text" @error('serial') class="error" @enderror wire:model="serial" name="serial" placeholder=" ">
            <span>{{__t('Серійний номер')}}</span>
            @error('serial') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="text" @error('date') class="error" @enderror wire:model="date" name="date" placeholder=" ">
            <span>{{__t('Дата купівлі')}}</span>
            @error('date') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <textarea @error('comment') class="error" @enderror wire:model.debounce.800ms.change="comment" name="comment" placeholder=" "></textarea>
            <span>{{__t('Детальний опис несправності або текст звернення')}} *</span>
            @error('comment') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="text" @error('complect') class="error" @enderror wire:model="complect" name="complect" placeholder=" ">
            <span>{{__t('Комплектність')}}</span>
            @error('complect') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        @include('livewire.form.partials.load-img')

        <div class="flex-row">
            @csrf
            @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif
        </div>
        <button type="submit" class="main-btn blue-big mt-8" name="submit" value="Submit" wire:loading.class="main-btn-disable" wire:loading.attr="disabled"  wire:target="submit">
            <span wire:loading.class="spinner" wire:target="submit"></span>
            <span wire:loading.remove wire:target="submit">{{__t('Надіслати запит')}}</span>
        </button>
    </form>

</div>
