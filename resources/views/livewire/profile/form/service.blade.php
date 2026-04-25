<div class="bottom-row flex pt-24 v--start" x-data="">
    <form wire:submit="submit" autocomplete="off" id="{{$formId}}" class="flex fd--column">
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
            <input type="text" @error('pib') class="error" @enderror wire:model.change="pib" name="pib" placeholder=" ">
            <span>{{__t('ПІБ')}} *</span>
            @error('pib') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="tel" class="tel-input @error('phone') error @enderror" wire:model.change="phone" name="phone" x-mask="+99(999) 999-99-99" placeholder="+38(0__) ___-__-__">
            @error('phone') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="text" @error('order_nom') class="error" @enderror wire:model.change="order_nom" name="order_nom" placeholder=" ">
            <span>{{__t('№ замовлення')}}</span>
            @error('order_nom') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="text" @error('product_name') class="error" @enderror wire:model.change="product_name" name="product_name" placeholder=" ">
            <span>{{__t('Назва товару')}}</span>
            @error('product_name') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="text" @error('serial') class="error" @enderror wire:model.change="serial" name="serial" placeholder=" ">
            <span>{{__t('Серійний номер')}}</span>
            @error('serial') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="text" @error('date') class="error" @enderror wire:model.change="date" name="date" placeholder=" ">
            <span>{{__t('Дата купівлі')}}</span>
            @error('date') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <textarea @error('comment') class="error" @enderror wire:model.change="comment" name="comment" placeholder=" "></textarea>
            <span>{{__t('Детальний опис несправності або текст звернення')}} *</span>
            @error('comment') <p class="error">{{ __t($message) }}</p> @enderror
        </lebel>
        <lebel class="input">
            <input type="text" @error('complect') class="error" @enderror wire:model.change="complect" name="complect" placeholder=" ">
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
            <span wire:target="submit">{{__t('Надіслати запит')}}</span>
        </button>

    </form>
    <div class="info p-24 flex fd--column">
        @php
            $phone = explode(',', setting('telinfophone'));
        @endphp
        <p class="fsz-18 fw-600">{{__t('Зв\'яжіться з нами')}}</p>
        @loop($phone as $tel)
        <a href="tel:{{$tel}}" class="color--black mt-24">{{$tel}}</a>
        @endloop
        <a href="mailto:{{setting('email-v-futere')}}" class="color--black mt-16">{{setting('email-v-futere')}}</a>
        <span class="mt-24 color--gray">{{__t('Центр обслуговування клієнтів працює')}} {!! setting('grafik-raboty-v-futere') !!}</span>
    </div>

</div>
