<div class="content">
    <div class="reviews-info-page pb-60" x-data="{form_open : false, reply_form_open : false}">
        <h2 class="fsz-34 fw-600 heading">{{__t('Відгуки про магазин')}}</h2>
        @php
            $comments_count = $comments->total();
        @endphp
        <div class="title-wrapper mt-24 p-16">
            <p class="fw-600">{{__t('Рейтинг')}}</p>
            <div class="title-wrapper__columns flex v--center h--wrap mt-16">
                @foreach ($ratingData as $rating)
                    <div class="column">
                        <div class="row flex v--center h--between">
                            <span class="fsz-13">{{ $rating['label'] }}</span>
                            <p class="fsz-13 fw-600">{{ $rating['count'] }}</p>
                        </div>
                        <div class="raiting-row relative mt-4">
                            <span style="width: {{ $rating['percentage'] }}%; background: {{ $rating['color'] }};"></span>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="main-btn blue-small get-review mt-24" @click="form_open = ! form_open">{{__t('Написати відгук')}}</button>
        </div>
        <div class="bottom-wrapper mt-24">

            @if($comments_count)

                <div class="flex-row flex v--center h--between">
                    <h3 class="fsz-24 fw-600 heading">{{$comments_count}} {{inflection($comments_count, [__t('відгук'), __t('відгука'), __t('відгуків')])}}</h3>

                    @include('partials.sorting')

                    {{--
                    <div class="sort-by flex v--center">
                        <span class="fsz-16 color--gray">Сортування:</span>
                        <div class="custom-select relative">
                            <div class="visible flex v--center">
                                <input type="text" readonly value="Популярні">
                                <div class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="4" viewBox="0 0 8 4" fill="none">
                                        <path d="M0 6.99382e-07L8 0L4 4L0 6.99382e-07Z" fill="#222222"/>
                                    </svg>
                                </div>
                                <div class="hidden">
                                    <ul class="flex fd--column">
                                        <li><span class="select-row current">Популярні</span></li>
                                        <li><span class="select-row">Від дешевих до дорогих </span></li>
                                        <li><span class="select-row">Від дорогих до дешевих</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
                <div class="rev-wrapper flex fd--column mt-16">
                    @foreach($comments as $comment)
                        <livewire:reviews.company.comment :$comment :key="$comment->id"/>
                    @endforeach
                    {{$comments->links()}}
                </div>

            @endif

            @if(!$comments_count)
                <p>{{__t('Поки що немає жодного відгуку. Залишіть відгук першим') }}</p>
            @endif

            @teleport('.reviews_form')
                @include('livewire.reviews.company.partials.comment-form',[
                    'method'=>'postComment',
                    'state'=>'newCommentState',
                    'inputId'=> 'comment',
                    'inputLabel'=> __t('Написати відгук'),
                    'button'=>__t('Надіслати відгук')
                ])
            @endteleport
        </div>
    </div>
</div>