<div class="{{$class}} favorite-button" data-id="{{ $product->id }}">
    <button class="like cart-btn" title="{{ __t('Список побажань') }}">
        @if(!empty($favBtnDell))
            <img src="/assets/images/trash.svg" alt="trash" class="icon">
        @else
            <img src="/assets/images/heart-gray.svg" alt="heart" class="icon">
        @endif
        <span class="black spinner compare-black" style="display: none;"></span>
    </button>
</div>
