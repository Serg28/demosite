<div class="reviews bl1" id="reviews" wire:loading.class="disabled">
    @php
        $comments_count = $comments->count();
    @endphp
    <div class="title">{{__t('Отзывы')}} <span class="count">{{$comments_count}}</span></div>
    <div class="info">
        <div class="info_wrap" x-data="{ expanded: false }">
            <div class="comment_bl_wrap" @if($comments_count>5) x-show="expanded" x-collapse.min.500px @endif>
                @if($comments_count)
                    @foreach($comments as $comment)
                        <livewire:comment :$comment :key="$comment->id"/>
                    @endforeach
                    {{$comments->links()}}

                @endif
                @if(!$comments_count)
                    <div class="border_bl" style="margin-top: -5px; margin-bottom: 13px;">
                        <p>{{__t('Пока нет ни одного отзыва. Оставьте отзыв первым') }}</p>
                    </div>
                @endif

            </div>
            @if($comments_count > 5)
                <div class="show-more-wrap start">
                    <button class="show-more_ show-all" :class="expanded ? 'show-min-comments' : ''" @click="expanded = ! expanded">
                        <span class="visible">{{__t('Показать еще') }}</span>
                        <span class="hidden">{{__t('Показать меньше') }}</span>
                        <img src="/img/arrow-down-orange.svg" alt="">
                    </button>
                </div>
            @endif
            {{--@auth--}}
            @include('commentify::livewire.partials.comment-form',[
                'method'=>'postComment',
                'state'=>'newCommentState',
                'inputId'=> 'comment',
                'inputLabel'=> __t('Написать отзыв'),
                'button'=>__t('Отправить отзыв')
            ])
            {{--@else
                <a class="mt-2 text-sm" href="/login">Log in to comment!</a>
            @endauth --}}

        </div>
    </div>
</div>

