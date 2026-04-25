<div class="border_bl">
        <div class="rev p-24">
            <div class="rev-top" itemprop="review" itemscope="itemscope" itemtype="http://schema.org/Review">
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
                            <rating-stars size="big">{{$comment->rating}}</rating-stars>
                        </div>
                    </div>
                </div>
                <p class="fsz-16 fw-400 pt-24 lh-140" itemprop="reviewBody">{!! $comment->presenter()->replaceUserMentions($comment->presenter()->markdownBody()) !!}</p>

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
                <button x-on:click="confirmCommentDeletion"
                        x-data="{confirmCommentDeletion(){
                            if(window.confirm('{{__t('Ви дійсно бажаєте видалити цей коментар?')}}')){
                               @this.call('deleteComment')
                            }}}"
                        class="acc-rev-btn flex v--center color--blue mt-24"><img src="/assets/images/trash-blue.svg" alt="{{__t('Видалити')}}">{{__t('Видалити')}}
                </button>
            </div>
        </div>

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


