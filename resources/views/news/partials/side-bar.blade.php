@if ($count)
<div class="side-bar p-24">
    <h2 class="fsz-24 fw-600 heading">{{__t('Останні новини')}}</a>
    <div class="news-rows mt-24 flex fd--column">
        @foreach($list as $newsItem)
            @php
                $url = $newsItem->getUrl()
            @endphp
            <div class="news-row flex v--center pb-16">
                <a href="{{$url}}" class="img">
                    <img src="{!! $newsItem->getImgPath(100, 100) !!}" alt="">
                </a>
                <div class="right flex fd--column">
                    <a href="{{$url}}" class="name fsz-16 fw-400 color--black">{{$newsItem->t('title')}}</a>
                    <span class="color--gray fw-400 fsz-13">{{$newsItem->created_at->format('d.m.Y')}}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif