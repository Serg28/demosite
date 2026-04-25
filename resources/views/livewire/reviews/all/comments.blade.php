
<div>
<div class="flex-wrap-container">
    <div class="reviews bl1" id="reviews" wire:loading.class="disabled" wire:target="postComment">
        @php
            $comments_count = $comments->count();
        @endphp
        <div class="info">
            <div class="info_wrap">
                <div class="comment_bl_wrap">
                    @if($comments_count)
                        @foreach($comments as $comment)
                            <livewire:reviews.all.comment :$comment :key="$comment->id"/>
                        @endforeach
                    @endif
                    @if(!$comments_count)
                        <div class="border_bl" style="position: relative;top: 50%;text-align: center;">
                            <p>{{__t('Пока нет ни одного отзыва. Оставьте отзыв первым') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="leave-review">
        <h3 class="heading screen-heading">{{__t('Добавьте ваш отзыв')}}</h3>
        <a href="{{geturl('/comments/index/c_company')}}" class="main-btn">{{__t('Отзыв о компании')}}</a>
        <a href="{{geturl('/comments/index/c_product')}}" class="main-btn">{{__t('Отзыв о товаре')}}</a>
    </div>
</div>
{{$comments->links()}}
</div>
