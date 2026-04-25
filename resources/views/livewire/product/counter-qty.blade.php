<div class="input-group">
    <button class="minus-item_ @if($quantity===$min) disabled @endif" type="button" wire:click="decrement" wire:loading.attr="disabled">
        <svg width="10" height="2" viewBox="0 0 10 2" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.74845e-07 -3.97627e-08L10 8.34465e-07L10 2L0 2L1.74845e-07 -3.97627e-08Z" fill="#171A20"/>
        </svg>
    </button>
    <input type="text" wire:model.live="quantity" class="item-quantity" value="1" onkeypress="return isNumberKey(event)" maxlength="3" wire:loading.attr="disabled">
    <button class="plus-item_ @if($quantity===$max) disabled @endif" type="button"  wire:click="increment" @if($quantity===$max) disabled @endif wire:loading.attr="disabled">
        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M4 10L4 -8.74206e-08L6 0L6 10L4 10Z" fill="#171A20"/>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.74845e-07 4L10 4L10 6L0 6L1.74845e-07 4Z" fill="#171A20"/>
        </svg>
    </button>
</div>