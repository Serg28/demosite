<div class="{{$class}}">
    <button class="like cart-btn @if(!empty($active)) active @endif" wire:click="update" wire:loading.class="loading" wire:target="update" title="{{__t('Список побажань')}}">
        @if(!empty($favBtnDell))
            <img src="/assets/images/trash.svg" alt="trash" wire:loading.class="hidden" wire:target="update">
        @else
            @if(!empty($active))
                <img src="/assets/images/heart-active.svg" alt="heart-active" wire:loading.class="hidden" wire:target="update">
            @else
                <img src="/assets/images/heart-gray.svg" alt="heart-gray" wire:loading.class="hidden" wire:target="update">
            @endif
        @endif


        <span class="black" wire:loading.class="spinner" wire:target="update"></span>
    </button>
</div>
