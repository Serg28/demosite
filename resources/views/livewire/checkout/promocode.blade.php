<div class="promo-wrap bg--light-blue br--br-4" x-data="{show: false}">
    <div class="visible-row flex v--center h--between" x-bind:class="show ? 'active' : ''"  @click="show = !show">
        <span class="fsz-14 color--blue">{{$promocode ? __t('Промокод застосований') : __t('У мене є промокод')}}</span>
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none">
                <path d="M0.375002 0.625001L6 6.25L11.625 0.625" stroke="#2264DC"/>
            </svg>
        </div>
    </div>
    <div class="hidden-row" x-bind:style="{display: show ? 'block' : 'none'}">
        <lebel class="input small flex v--center">
            <input type="text" wire:model="promocode" class="@error('promocode') error @enderror" placeholder=" ">
            <span>{{__t('Промокод')}}</span>
            <div class="closer input-placeholder-clear" wire:click="resetPromocode" wire:target="resetPromocode"><img src="/assets/images/closer.svg" alt=""></div>
            @if(!$promocode)
            <button class="main-btn blue-small" type="button" wire:click="setPromocode" wire:target="setPromocode">
                <span wire:loading.class="spinner"></span>
                Ok
            </button>
            @endif
        </lebel>
        @if(!$promocode)
        <p class="error">{{$message}}</p>
        @endif
    </div>
</div>





















