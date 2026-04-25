@cache('lastNews_'.$cacheKey,86400)
@if($newsLast)
<div class="blog-section">
    <div class="container">
        <div class="flex-row flex v--center h--between">
            <h2 class="fsz-28 fw-600 heading">{{ $lastNews->h2->t('title') }}</h2>
            <a href="{{route('blog')}}" class="custom-btn v--center fsz-18 fw-500 color--blue">{{__t('Дивитись всі')}} <img src="/assets/images/arrow-blue-right.svg" alt=""></a>
        </div>
        <div class="blog-section__wrap flex h--wrap">
            <div class="blog-swiper swiper">
                <div class="swiper-wrapper">
                    @loop($newsLast as $newsItem)
                    @php
                        $title = $newsItem->t('title');
                        $url = $newsItem->getUrl();
                    @endphp
                    <div class="swiper-slide news">
                        <a href="{{$url}}" class="image">
                            <img src="{!! $newsItem->getImgPath(328, 200) !!}" alt="{{ $title }}" title="{{ $title }}">
                        </a>
                        <a href="{{$url}}" class="news-name fsz-16 fw-600 color--black">{{$title}}</a>
                        <span class="date fsz-13 color--gray">{{$newsItem->date()}}</span>
                    </div>
                    @endloop
                </div>
                <div class="swiper-pagination blog-pagination"></div>
            </div>
        </div>
    </div>
</div>
@endif
@endcache

