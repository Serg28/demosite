<div class="account-page__content" x-data="{form_open : false, reply_form_open : false}">
    <h2 class="fsz-28 fw-600 mb-24 content-heading">{{ __t('Мої відгуки') }}</h2>
    <div class="account-reviews flex fd--column">


        @loop($productsWithComments as $product)
            @php
                $comments = $product->product->comments;
                $imgPath = !empty($product->product->picture) ? $product->product->getImgPath(75, '') : glide($product->product->firstOtherPicture, ['w' => 75]);
            @endphp
            <div class="account-reviews-row br--br-4 bg--white" wire:key="order-product-{{$product->product_id}}" x-data="{opened : false}">
                <div class="visible flex v--center h--between">
                    <div class="left flex v--center">
                        <img src="{{$imgPath}}" alt="{{e($product->product->t('title'))}}">
                        <a href="{{$product->product->getUrl()}}#reviews" target="_blank" class="color--black">{{ $product->product->t('title') }}</a>
                    </div>
                    @if($comments?->isNotEmpty())
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="9" viewBox="0 0 14 9" fill="none">
                                <path d="M1 1L7 7L13 1" stroke="#2264DC" stroke-width="2"/>
                            </svg>
                        </div>
                    @else
                        <button class="main-btn border-small js-lw-modal" data-component="profile.reviews.post-comment" data-id="{{$product->product->id}}">{{__t('Залишити відгук')}}</button>
                    @endif
                </div>
                <div class="hidden">
                    @if($comments?->isNotEmpty())
                        @loop($comments as $comment)
                            <livewire:profile.reviews.comment :$comment :key="$comment->id"/>
                        @endloop
                    @else
                        <p class="p-24">{{ __t('Поки що немає жодного відгуку. Залишіть відгук першим') }}</p>
                    @endif
                </div>
            </div>
        @endloop
        {{ $productsWithComments->links() }}
    </div>
</div>
