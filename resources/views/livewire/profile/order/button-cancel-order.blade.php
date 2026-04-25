<div>
    @if($show)
    <button class="main-btn bg-yellow mw-a" wire:click="cancel({{$orderId}})" wire:loading.class="disabled"
            wire:loading.attr="disabled" wire:target="cancel">
        <span wire:loading.class="spinner pa m0" wire:target="cancel"></span>
        <span wire:loading.class="opacity-0" wire:target="cancel">{{__t('Скасувати')}}</span>
    </button>
    @endif
</div>
