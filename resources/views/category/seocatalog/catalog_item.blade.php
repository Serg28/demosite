<div class="catalog-card">
    @if($category->is_active && !$category->treemenu)
        {{-- Если не имеет типа меню Ссылка --}}

        <div class="image">
            <img loading="lazy" src="{!! $category->getImgPath('', 300) !!}" height="120"
                 title="{{$category->t('title')}}" alt="{{$category->t('title')}}">
        </div>
        <a href="{{$category->getUrl()}}" @if($category->is_target_blank) target="_blank" @endif >
            <p class="name fw-600 fsz-16">{{$category->t('title')}}</p>
        </a>

        {{-- /Если не имеет типа меню Ссылка --}}
    @else

        @if($category->is_active && count($category->children)>0)

            <div class="image">
                <img loading="lazy" src="{!! $category->getImgPath('', 300) !!}" height="120" title="{{$category->t('title')}}" alt="{{$category->t('title')}}">
            </div>

            {{-- Заголовок как ссылка --}}
            {{--<p class="name fw-600 fsz-16">
                @if($category->is_active)
                    <a href="{{$category->getUrl()}}">{{$category->t('title')}}</a>
                @else
                    {{$category->t('title')}}
                @endif
            </p> --}}
            {{-- /Заголовок как ссылка --}}

            <p class="name fw-600 fsz-16">
                {{$category->t('title')}}
            </p>

            <ul class="flex fd--column">
                @foreach($category->children as $rubric)
                    @if($rubric->is_active)
                    <li>
                        <a href="{!! $rubric->getTreeUrl() !!}"
                        @if($rubric->treemenu) {!! $rubric->treemenu->is_target_blank ? 'target="_blank"' : '' !!} @endif>
                            {{$rubric->t('title')}}
                        </a>
                    </li>
                    @endif
                @endforeach
            </ul>
            @if($category->is_active)
            <a href="{!! $category->getTreeUrl() !!}"  @if($category->treemenu) {!! $category->treemenu->is_target_blank ? 'target="_blank"' : '' !!} @endif class="custom-btn">{{__t('Дивитись всі')}} <img src="/assets/images/arrow-blue-right.svg" alt="{{$category->t('title')}}"></a>
            @endif
        @endif
    @endif
</div>