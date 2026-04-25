<div class="border_bl">
    @if($isEditing)
        @include('commentify::livewire.partials.comment-form',[
            'method'=>'editComment',
            'state'=>'editState',
            'inputId'=> 'reply-comment',
            'inputLabel'=> __t('Редактирование отзыва'),
            'button'=> __t('Сохранить отзыв')
        ])
    @else
        <div class="review client"  itemprop="review" itemscope="itemscope" itemtype="http://schema.org/Review">
            <div class="top_bl">
                <div class="name_grade">
                    @php
                        $username = Str::ucfirst($comment->user?->first_name ?: ($comment->name ?: __t('Анонимный посетитель')));
                        $type = ($comment->commentable instanceof App\Models\Product) ? 'product' : 'company';
                    @endphp
                    <p itemprop="author" itemscope="itemscope" itemtype="http://schema.org/Person">{{$username}}</p>
                    <span class="about about-{{$type}}">{{$type=='product' ? __t('о товаре') : __t('о компании') }}</span>
                    <meta itemprop="name" content="{{$username}}"/>
                    <meta itemprop="datePublished" content="{{$comment->created_at}}">
                    @if($comment->rating>0)
                    <div class="stars-wrap d--flex ai--center" x-data="{ rating: {{ $comment->rating ?? 0}} }">
                            <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                <img :src="star > rating ? '/img/star-gray.svg' : '/img/star-purple.svg'" alt="">
                            </template>
                    </div>
                    @endif

                    @if ($comment->commentable instanceof App\Models\Product)
                    <a href="{{$comment->commentable->getUrl() ?? '#'}}" target="_blank" class="prod-link">{{$comment->commentable->getArticle()}}</a>
                    @endif

                </div>
                @php
                    $relativeCreatedAt = $comment->presenter()->relativeCreatedAt();
                @endphp
                <div class="date" datetime="{{$relativeCreatedAt}}"
                     title="{{$relativeCreatedAt}}">{{$relativeCreatedAt}}</div>
            </div>
            @if(!$comment->user?->inGroups(['admin', 'manager']))
                @if($comment->isProductPurchasedByUser())
                    <div class="whose_entry">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="10" fill="#24AC05"/>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M15.7385 4.48923C16.3362 4.85412 16.4977 5.59363 16.0992 6.14096L9.16316 15.6696C8.94703 15.9665 8.59694 16.159 8.20921 16.1942C7.82148 16.2294 7.43696 16.1035 7.16146 15.8511L3.69341 12.6749C3.18553 12.2098 3.18553 11.4556 3.69341 10.9905C4.2013 10.5253 5.02474 10.5253 5.53262 10.9905L7.87893 13.1393L13.9351 4.81958C14.3335 4.27224 15.1409 4.12434 15.7385 4.48923Z"
                                  fill="white"/>
                        </svg>
                        {{__t('Отзыв покупателя') }}
                    </div>
                @endif
            @else
                <div class="whose_entry spec">{{__t('Специалист Сервис-Маркет')}}</div>
            @endif
            <div itemprop="reviewBody" >{!! $comment->presenter()->replaceUserMentions($comment->presenter()->markdownBody()) !!}</div>
            <div class="comment-block-btn reviews">
                {{--
                <button wire:click="$toggle('showOptions')" class="btn btn-comment btn-comment-option" type="button">
                    <svg class="" width="20" height="20" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"></path>
                    </svg>
                </button>
                @if($showOptions)
                    <div class="comment-block-list-options">
                        <ul class="comment-list-options">
                            @can('update',$comment)
                            <li>
                                <button wire:click="$toggle('isEditing')" type="button" class="">{{__t('Редактировать')}}</button>
                            </li>
                            @endcan
                            @can('destroy',$comment)
                            <li>

                                <button
                                        x-on:click="confirmCommentDeletion"
                                        x-data="{
                                                            confirmCommentDeletion(){
                                                                if(window.confirm('You sure to delete this comment?')){
                                                                    @this.call('deleteComment')
                                                                }
                                                            }
                                                        }"
                                        class="">{{__t('Удалить')}}
                                </button>
                            </li>
                            @endcan
                        </ul>
                    </div>
                @endif
                <livewire:like :$comment :key="$comment->id"/>--}}
                <a href="#" wire:click.prevent="$toggle('isReplying')" class="btn btn-comment btn-comment-reply">{{__t('Ответить') }}</a>
            </div>
        </div>

        @if(!$hasReplies)
            @foreach($comment->children as $child)
                @include('commentify::livewire.partials.comment-reply-comment',[
                  'comment'=> $child,
              ])
            @endforeach

            {{-- View all Replies ({{$comment->children->count()}})--}}
        @endif
        {{-- {{$comment->children->count()}}--}}

    @endif
    @if($isReplying)
        @include('commentify::livewire.partials.comment-form',[
           'method'=>'postReply',
           'state'=>'replyState',
           'inputId'=> 'reply-comment',
           'inputLabel'=> __t('Написать ответ'),
           'button'=> __t('Отправить ответ')
       ])
    @endif
    {{--    @if($hasReplies)--}}

    {{--            ---2------}}

    {{--            <article class="p-1 mb-1 ml-1 lg:ml-12 border-t border-gray-200 dark:border-gray-700 dark:bg-gray-900">--}}
    {{--            @foreach($comment->children as $child)--}}
    {{--                <livewire:comment :comment="$child" :key="$child->id"/>--}}
    {{--            @endforeach--}}
    {{--        </article>--}}
    {{--        ---2------}}

    {{--    @endif--}}
    <script>
        function detectAtSymbol() {
            const textarea = document.getElementById('reply-comment');
            if (!textarea) {
                return;
            }

            const cursorPosition = textarea.selectionStart;
            const textBeforeCursor = textarea.value.substring(0, cursorPosition);
            const atSymbolPosition = textBeforeCursor.lastIndexOf('@');

            if (atSymbolPosition !== -1) {
                const searchTerm = textBeforeCursor.substring(atSymbolPosition + 1);
                if (searchTerm.trim().length > 0) {
                    @this.
                    dispatch('getUsers', {searchTerm: searchTerm});
                }
            }
        }
    </script>

</div>


