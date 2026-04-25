<div class="border_bl">
    @if($isEditing)
        @teleport('.reviews_form')
            @include('livewire.reviews.product.partials.comment-form',[
                'method'=>'editComment',
                'state'=>'editState',
                'inputId'=> 'reply-comment',
                'inputLabel'=> __t('Редагування відгуку'),
                'button'=> __t('Зберегти відгук')
            ])
        @endteleport
    @else
        <div class="rev">
            <div class="rev-top p-24" itemprop="review" itemscope="itemscope" itemtype="http://schema.org/Review">
                @php
                    $isAdmin = $comment->user?->inGroups(['admin', 'manager']);
                    $username = $isAdmin ? __t('Спеціаліст Smart Mag') : Str::ucfirst($comment->user?->first_name ?: ($comment->name ?: __t('Анонімний відвідувач')));
                    $relativeCreatedAt = $comment->presenter()->relativeCreatedAt();
                    $avatar = $comment->user?->avatar() ?? '';
                @endphp
                <div class="top-row flex v--center">
                    <div class="left-content a">
                        @if($avatar)
                            <img loading="lazy" src="{{$avatar}}" alt="{{e($username)}}" width="52" height="52">
                        @else
                            {{Str::substr($username, 0, 1)}}
                        @endif
                    </div>
                    <div class="right-content">
                        <meta itemprop="name" content="{{$username}}"/>
                        <meta itemprop="datePublished" content="{{$comment->created_at}}">
                        <p class="name fsz-16 fw-600" itemprop="author" itemscope="itemscope" itemtype="http://schema.org/Person">{{$username}}</p>

                        <div class="flex-row flex v--center">
                            <span class="fsz-14 color--gray date">{{$relativeCreatedAt}}</span>
                            @if(!$isAdmin)
                                <rating-stars size="big">{{$comment->rating}}</rating-stars>
                            @endif
                        </div>
                    </div>
                </div>
                <p class="fsz-16 fw-400 pt-24 lh-140" itemprop="reviewBody">{!! $comment->presenter()->replaceUserMentions($comment->presenter()->markdownBody()) !!}</p>

                @if(!$isAdmin)
                    @if($comment->plus_text)
                        <div class="rev-row mt-24">
                            <p class="fsz-14 fw-600">{{ __t('Плюси:') }}</p>
                            <p class="fsz-16 fw-400 mt-8">{!! $comment->plus_text !!}</p>
                        </div>
                    @endif
                    @if($comment->minus_text)
                        <div class="rev-row mt-24">
                            <p class="fsz-14 fw-600">{{ __t('Мінуси:') }}</p>
                            <p class="fsz-16 fw-400 mt-8">{!! $comment->minus_text !!}</p>
                        </div>
                    @endif
                @endif


                <div class="row mt-24 flex v--center">
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

                    <div class="ans flex v--center color--blue" wire:click.prevent="$toggle('isReplying')" @click="reply_form_open = true">
                        <div class="icon flex v--center h--center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M10.5 4.875L15 9.375L10.5 13.875" stroke="#2264DC"/>
                                <path d="M15 9.375L3 9.375L3 3.75" stroke="#2264DC"/>
                            </svg>
                        </div>
                        {{__t('Відповісти')}}
                    </div>
                    <livewire:like :$comment :key="$comment->id"/>
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
        </div>
    @endif
    @if($isReplying)
        @teleport('.reviews_reply_form')
            @include('livewire.reviews.product.partials.comment-form-replay',[
               'method'=>'postReply',
               'state'=>'replyState',
               'inputId'=> 'reply-comment',
               'inputLabel'=> __t('Написати відповідь'),
               'button'=> __t('Надіслати відповідь')
           ])
        @endteleport
    @endif

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


