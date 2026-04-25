<div class="left flex fd--column" wire:loading.class="disabled">
    @php
        $comments_count = $comments->count();
    @endphp

                @if($comments_count)
                    @foreach($comments as $comment)
                        <livewire:comment :$comment :key="$comment->id"/>
                    @endforeach
                    {{$comments->links()}}

                @endif
                @if(!$comments_count)
                    <p>{{__t('Пока нет ни одного отзыва. Оставьте отзыв первым') }}</p>
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

