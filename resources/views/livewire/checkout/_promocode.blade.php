<div class="promo" x-data="{show: false}" >
    <div class="visible">
        <p>{{__t('Промокод')}}</p>
        <button type="button" class="open-button_" @click="show = !show">
            <img :src="show ? '/img/minus.svg' : '/img/plus.svg'" alt="" class="open">
            <span class="open" x-text="show ? '{{__t('Згорнути')}}' : ({{ $promocode ? '__t(\'Відкрити\')' : '__t(\'Додати\')' }})"></span>
        </button>
    </div>
    <div class="hidden promo-block" x-bind:style="{display: show ? 'block' : 'none'}">

        {{--
        @if($promocode)

            <p style="font-size: 18px">{{__t('Застосований')}}</p>

            <button type="button" wire:click="resetPromocode" wire:target="resetPromocode" class="main-btn main-btn--red-transparent">
                <span wire:loading.class="spinner" ></span>
                <span wire:loading.remove>{{__t('Скасувати')}}</span>
                <span wire:loading> {{__t('Подождите...')}}</span>
            </button>

        @else

            <input type="text" wire:model="promocode" name="promocode" class="@error('promocode') error @enderror">
            <p class="error">{{$message}}</p>

            <button type="button" wire:click="setPromocode" wire:target="setPromocode" class="main-btn main-btn--red-transparent">
                <span wire:loading.class="spinner"></span>
                <span wire:loading.remove>{{__t('Застосувати')}}</span>
                <span wire:loading> {{__t('Подождите...')}}</span>
            </button>

        @endif --}}

        <!--[if BLOCK]><![endif]--> <!-- [tl! highlight] -->
        @if($promocode)
            <p style="font-size: 18px">{{__t('Застосований')}}</p>

            <button type="button" wire:click="resetPromocode" wire:target="resetPromocode" class="main-btn main-btn--red-transparent">
                <span wire:loading.class="spinner" ></span>
                <span wire:loading.remove>{{__t('Скасувати')}}</span>
                <span wire:loading> {{__t('Подождите...')}}</span>
            </button>
        @endif
        <!--[if ENDBLOCK]><![endif]--> <!-- [tl! highlight] -->

        <!--[if BLOCK]><![endif]--> <!-- [tl! highlight] -->
        @if(!$promocode)
            <input type="text" wire:model="promocode" name="promocode" class="@error('promocode') error @enderror">
            <p class="error">{{$message}}</p>

            <button type="button" wire:click="setPromocode" wire:target="setPromocode" class="main-btn main-btn--red-transparent">
                <span wire:loading.class="spinner"></span>
                <span wire:loading.remove>{{__t('Застосувати')}}</span>
                <span wire:loading> {{__t('Подождите...')}}</span>
            </button>
        @endif
        <!--[if ENDBLOCK]><![endif]--> <!-- [tl! highlight] -->


    </div>

</div>