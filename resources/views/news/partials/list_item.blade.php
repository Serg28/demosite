<div class="blog__wrap flex v--center h--wrap mt-24">
    @if ($count)
        @foreach($list as $newsItem)
            @php
                $url = $newsItem->getUrl()
            @endphp

            <div class="news flex fd--column">
                <a href="{{$url}}" class="img">
                    <img loading="lazy" src="{!! $newsItem->getImgPath(540, '') !!}" alt="{{ e($newsItem->t('title')) }}" width="540" height="361">
                </a>
                <a href="{{$url}}" class="name fw-600 color--black mt-16">{{$newsItem->t('title')}}</a>
                <span class="mt-16 colo--gray fsz-13">{{$newsItem->date()}}</span>
            </div>
        @endforeach
    @else
        {{__t('В данной категории нет новостей')}}
    @endif
</div>
