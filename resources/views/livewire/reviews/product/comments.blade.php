<div class="container">
    <h2 class="fsz-28 fw-600 heading">{{ __t('Відгуки покупців') }} <span>({{$model->count_comments}})</span></h2>


    <div class="product-reviews-screen__wrap flex v--start h--between" id="reviews" x-data="{form_open : false, reply_form_open : false}">
        <div class="left flex fd--column" wire:loading.class="disabled">
            @php
                $comments_count = $model->count_comments;
            @endphp

            @if($comments_count)
                @foreach($comments as $comment)
                    <livewire:reviews.product.comment :$comment :key="$comment->id"/>
                @endforeach
                {{$comments->links()}}
            @endif
            @if(!$comments_count)
                <p>{{__t('Поки що немає жодного відгуку. Залишіть відгук першим') }}</p>
            @endif

            @teleport('.reviews_form')
                @include('livewire.reviews.product.partials.comment-form',[
                    'method'=>'postComment',
                    'state'=>'newCommentState',
                    'inputId'=> 'comment',
                    'inputLabel'=> __t('Написати відгук'),
                    'button'=>__t('Надіслати відгук')
                ])
            @endteleport
        </div>

        <div class="right flex fd--column">
            <div class="img">
                @php
                    $img = !empty($model->picture) ? $model->picture : $model->firstOtherPicture;
                @endphp
                <img loading="lazy" src="{{glide($img, ['w' => 190, 'h' => 190])}}" alt="{{ $model->t('title') }} " title="{{ $model->t('title') }}">
            </div>
            <p class="name fsz-16 fw-400">{{$model->t('title')}}</p>
            <span class="fsz-12 color--gray">{{__t('Код товару:')}} {{ $model->getArticle() }}</span>
            <button class="main-btn blue-small _get-review" @click="form_open = ! form_open">{{__t('Написати відгук')}}</button>
        </div>
    </div>
</div>
