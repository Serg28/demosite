<div class="blog-section">
    <div class="container">
        @if ($count)
        <div class="flex-row flex v--center h--between">
            <h2 class="fsz-28 fw-600 heading">{{__t('Читайте також')}}</h2>
            <a href="{{ route('blog') }}" class="custom-btn v--center fsz-18 fw-500 color--blue">{{__t('Дивитись всі')}} <img src="/assets/images/arrow-blue-right.svg" alt=""></a>
        </div>
        <div class="blog-section__wrap flex h--wrap">
            <div class="blog-swiper swiper" style="width: 100%">
                <div class="swiper-wrapper">
                    @foreach($list as $newsItem)
                        @php
                            $url = $newsItem->getUrl()
                        @endphp
                        <div class="swiper-slide news">
                            <a href="{{$url}}" class="image">
                                <img src="{!! $newsItem->getImgPath(328, 200) !!}" alt="">
                            </a>
                            <a href="{{$url}}" class="news-name fsz-16 fw-600 color--black">{{$newsItem->t('title')}}</a>
                            <span class="date fsz-13 color--gray">{{$newsItem->t('title')}}</span>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination blog-pagination"></div>
            </div>
        </div>
        @endif
    </div>
</div>
