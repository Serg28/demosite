@if(isset($sliderBrand->favoriteCategories))
<div class="popular">
    <p class="fsz-20 fw-600">{!! $sliderBrand->h2->t('title') ?? $page->t('title') !!}</p>
    <div class="scrl">
        <div class="popular__wrap grid v--center h--between">
            @loop($sliderBrand->favoriteBrends as $item)
            <div class="popular__product">
                <a href="{{$item->getUrl()}}"><img loading="lazy" src="{{$item->getImgPath('', 90)}}" alt="{{e($item->t('title'))}}"></a>
            </div>
            @endloop

            <div class="popular__product">
                <a href="{{$sliderBrand->contactsWithMap->t('adress')}}">{{$sliderBrand->contactsWithMap->t('title')}}<span class="icon"><img src="/assets/images/arrow-blue-right.svg" alt=""></span></a>
            </div>
        </div>
    </div>
</div>
@endif