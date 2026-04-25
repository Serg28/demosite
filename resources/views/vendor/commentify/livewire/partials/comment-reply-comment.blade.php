<div class="rev-ans p-24">
    @php
        $isAdmin = $comment->user?->inGroups(['admin', 'manager']);
        $username = $isAdmin ? __t('Спеціаліст Smart Mag') : Str::ucfirst($comment->user?->first_name ?: ($comment->name ?: __t('Анонимный посетитель')));
        $relativeCreatedAt = $comment->presenter()->relativeCreatedAt();
    @endphp
    <div class="rev-ans-row p-24">
        <div class="top-row flex v--center">
            <div class="left-content a">
                <img src="/assets/images/avatar.svg" alt="">
            </div>
            <div class="right-content">
                <p class="name fsz-16 fw-600">{{ $username }}</p>
                <div class="flex-row flex v--center">
                    <span class="fsz-14 color--gray date">{{$relativeCreatedAt}}</span>
                </div>
            </div>
        </div>
        <p class="mt-24">{!! $comment->presenter()->replaceUserMentions($comment->presenter()->markdownBody()) !!}</p>
    </div>

    {{-- {{$comment->presenter()->relativeCreatedAt()}} --}}
    {{-- Кнопки управления комментарием и лайк --}}
    {{--
    <div class="comment-block-btn comment-block-btn-reply">
        <button wire:click="$toggle('showOptions')" class="btn btn-comment-option" type="button" >
            <svg class="" width="20" height="20" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                 xmlns="http://www.w3.org/2000/svg"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"></path>
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
                        <button x-on:click="confirmCommentDeletion"
                                x-data="{confirmCommentDeletion(){if(window.confirm('{{__t('Вы уверены, что хотите удалить этот комментарий?')}}')){ @this.call('deleteComment')} } }"
                                class="">{{__t('Удалить')}}</button>
                    </li>
                    @endcan
                </ul>
            </div>
        @endif
        <livewire:like :$comment :key="$comment->id"/>
    </div> --}}
</div>





