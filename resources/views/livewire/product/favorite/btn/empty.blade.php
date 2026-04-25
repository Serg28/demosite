<div class="{{$class}}">
    @if(!empty(app('user')))
        <button class="like cart-btn" >
            @if(!empty($favBtnDell))
                <img src="/assets/images/trash.svg" alt="trash">
            @else
                <img src="/assets/images/heart-gray.svg" alt="">
            @endif
        </button>
    @endif
</div>
