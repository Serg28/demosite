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

        <div class="rev">
            <div class="rev-top p-24" itemprop="review" itemscope="itemscope" itemtype="http://schema.org/Review">
                @php
                    $isAdmin = $comment->user?->inGroups(['admin', 'manager']);
                    $username = $isAdmin ? __t('Спеціаліст Smart Mag') : Str::ucfirst($comment->user?->first_name ?: ($comment->name ?: __t('Анонимный посетитель')));
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
                            <div class="stars flex v--center">
                                @for ($i = 1; $i <= 5; $i++)
                                    <img src="{{ $i <= $comment->rating ? '/assets/images/star-full.svg' : '/assets/images/star-empty.svg' }}" alt="{{$i}}">
                                @endfor
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <p class="fsz-16 fw-400 pt-24 lh-140" itemprop="reviewBody">{!! $comment->presenter()->replaceUserMentions($comment->presenter()->markdownBody()) !!}</p>

                @if(!$isAdmin)
                <div class="rev-row mt-24">
                    <p class="fsz-14 fw-600">{{ __t('Плюси:') }}</p>
                    <p class="fsz-16 fw-400 mt-8"></p>
                </div>
                <div class="rev-row mt-24">
                    <p class="fsz-14 fw-600">{{ __t('Мінуси:') }}</p>
                    <p class="fsz-16 fw-400 mt-8"></p>
                </div>
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

                    <div class="ans flex v--center color--blue" wire:click.prevent="$toggle('isReplying')">
                        <div class="icon flex v--center h--center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M10.5 4.875L15 9.375L10.5 13.875" stroke="#2264DC"/>
                                <path d="M15 9.375L3 9.375L3 3.75" stroke="#2264DC"/>
                            </svg>
                        </div>
                        {{__t('Відповісти')}}
                    </div>

                    <livewire:like :$comment :key="$comment->id"/>

                    {{--
                    <div class="btn ok flex ml-auto v--center h--center color--gray-100">
                        <div class="icon flex v--center h--center mr-8">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M12.1667 6.6671H17.5002C18.4207 6.6671 19.1668 7.4133 19.1668 8.33375V10.0874C19.1668 10.3051 19.1242 10.5207 19.0413 10.722L16.4627 16.9844C16.3341 17.2967 16.0297 17.5004 15.6921 17.5004H1.66683C1.2066 17.5004 0.833496 17.1273 0.833496 16.6671V8.33375C0.833496 7.87353 1.2066 7.50044 1.66683 7.50044H4.56836C4.83914 7.50044 5.09302 7.36888 5.24917 7.14767L9.79366 0.709625C9.91241 0.541401 10.1362 0.485144 10.3204 0.577232L11.8322 1.33309C12.7085 1.77128 13.1611 2.76093 12.9194 3.71045L12.1667 6.6671ZM5.8335 8.82333V15.8337H15.134L17.5002 10.0874V8.33375H12.1667C11.0794 8.33375 10.2833 7.30956 10.5515 6.25592L11.3042 3.29928C11.3526 3.10937 11.2621 2.91144 11.0867 2.8238L10.5358 2.54833L6.61079 8.10881C6.40254 8.40383 6.13634 8.64566 5.8335 8.82333ZM4.16683 9.16708H2.50016V15.8337H4.16683V9.16708Z" fill="#AFB1C4"/>
                            </svg>
                        </div>
                        <span>1</span>
                    </div>
                    <div class="btn not-ok ml-16 flex v--center h--center color--gray-100">
                        <div class="icon flex v--center h--center mr-8">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M7.83364 13.3333H2.50016C1.57969 13.3333 0.833496 12.5872 0.833496 11.6667V9.91308C0.833496 9.69533 0.876146 9.47975 0.959029 9.2785L3.53767 3.01604C3.66625 2.70379 3.97055 2.5 4.30824 2.5H18.3335C18.7937 2.5 19.1668 2.8731 19.1668 3.33333V11.6667C19.1668 12.1269 18.7937 12.5 18.3335 12.5H15.432C15.1612 12.5 14.9073 12.6316 14.7512 12.8528L10.2067 19.2908C10.0879 19.459 9.86408 19.5153 9.67991 19.4232L8.16819 18.6673C7.29181 18.2292 6.83922 17.2395 7.08095 16.29L7.83364 13.3333ZM14.1668 11.1771V4.16667H4.86632L2.50016 9.91308V11.6667H7.83364C8.92091 11.6667 9.717 12.6908 9.44883 13.7445L8.69608 16.7012C8.64775 16.8911 8.73825 17.089 8.91358 17.1767L9.46449 17.4521L13.3896 11.8917C13.5978 11.5966 13.864 11.3548 14.1668 11.1771ZM15.8335 10.8333H17.5002V4.16667H15.8335V10.8333Z" fill="#AFB1C4"/>
                            </svg>
                        </div>
                        <span>0</span>
                    </div> --}}
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
            @include('commentify::livewire.partials.comment-form',[
               'method'=>'postReply',
               'state'=>'replyState',
               'inputId'=> 'reply-comment',
               'inputLabel'=> __t('Написать ответ'),
               'button'=> __t('Отправить ответ')
           ])
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


