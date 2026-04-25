<div class="screen screen-1">
    <div class="galerry-swiper-wrapper flex v--center h--between">
        <div class="swiper gallery-thumbs">
            <div class="swiper-wrapper">

                @if(!empty($page->picture))
                <div class="swiper-slide">
                    <img loading="lazy" src="{{$page->getImgPath(40, 40)}}" alt="{{ e($page->t('title')) }}">
                </div>
                @endif

                @if(!empty($otherPictures))
                    @foreach($otherPictures as $pictureOriginal => $pictureSmall)
                        <div class="swiper-slide">
                            <img loading="lazy" src="{{glide($pictureOriginal, ['w' => 40, 'h' => 40])}}" alt="{{ e($page->t('title')) }}  {{__t('фото')}} №{{$loop->index}}">
                        </div>
                    @endforeach
                @endif

                @if(empty($page->picture) && empty($otherPictures))
                    <div class="swiper-slide">
                        <img loading="lazy" src="{!! glide($page->firstOtherPicture, ['w'=>40, 'h'=>40]) !!}" alt="{{e($page->t('title'))}}">
                    </div>
                @endif

            </div>
        </div>
        <div class="gallery-swiper swiper">
            <div class="swiper-wrapper">
                @if(!empty($page->picture))
                <div class="swiper-slide">
                    <img loading="lazy" src="{{$page->getImgPath(456, 456)}}" alt="{{ e($page->t('title')) }}">
                </div>
                @endif
                @if(!empty($otherPictures))
                    @foreach($otherPictures as $pictureOriginal => $pictureSmall)
                        <div class="swiper-slide">
                            <img loading="lazy" src="{{glide($pictureOriginal, ['w' => 456, 'h' => 456])}}" alt="{{ e($page->t('title')) }}  {{__t('фото')}} №{{$loop->index}}">
                        </div>
                    @endforeach
                @endif

                @if(empty($page->picture) && empty($otherPictures))
                    <div class="swiper-slide">
                        <img loading="lazy" src="{!! glide($page->firstOtherPicture, ['w'=>456, 'h'=>456]) !!}" alt="{{e($page->t('title'))}}">
                    </div>
                @endif
            </div>
            <div class="swiper-pagination"></div>
            <div class="gallery-swiper-btn-prev gallery-btn"><img src="/assets/images/arrow-blue-left.svg" alt=""></div>
            <div class="gallery-swiper-btn-next gallery-btn"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></div>
        </div>
    </div>
</div>
{{-- <div class="screen screen-2">
    <div class="galerry-swiper-wrapper flex v--center h--between">
        <div class="swiper gallery-thumbs">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i1.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i2.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i3.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i4.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i5.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i6.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i7.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i8.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i1.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i2.png" alt="">
                </div>
            </div>
        </div>
        <div class="gallery-swiper swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
            </div>
            <div class="swiper-paginate"></div>
            <div class="gallery-swiper-btn-prev gallery-btn"><img src="/assets/images/arrow-blue-left.svg" alt=""></div>
            <div class="gallery-swiper-btn-next gallery-btn"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></div>
        </div>
    </div>
</div>
<div class="screen screen-3">
    <div class="galerry-swiper-wrapper flex v--center h--between">
        <div class="swiper gallery-thumbs">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i1.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i2.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i3.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i4.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i5.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i6.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i7.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i8.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i1.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i2.png" alt="">
                </div>
            </div>
        </div>
        <div class="gallery-swiper swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
            </div>
            <div class="swiper-paginate"></div>
            <div class="gallery-swiper-btn-prev gallery-btn"><img src="/assets/images/arrow-blue-left.svg" alt=""></div>
            <div class="gallery-swiper-btn-next gallery-btn"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></div>
        </div>
    </div>
</div>
<div class="screen screen-4">
    <div class="galerry-swiper-wrapper flex v--center h--between">
        <div class="swiper gallery-thumbs">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i1.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i2.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i3.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i4.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i5.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i6.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i7.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i8.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i1.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i2.png" alt="">
                </div>
            </div>
        </div>
        <div class="gallery-swiper swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
            </div>
            <div class="swiper-paginate"></div>
            <div class="gallery-swiper-btn-prev gallery-btn"><img src="/assets/images/arrow-blue-left.svg" alt=""></div>
            <div class="gallery-swiper-btn-next gallery-btn"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></div>
        </div>
    </div>
</div>
<div class="screen screen-5">
    <div class="galerry-swiper-wrapper flex v--center h--between">
        <div class="swiper gallery-thumbs">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i1.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i2.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i3.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i4.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i5.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i6.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i7.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i8.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i1.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i2.png" alt="">
                </div>
            </div>
        </div>
        <div class="gallery-swiper swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="/assets/images/prod-i.png" alt="">
                </div>
            </div>
            <div class="swiper-paginate"></div>
            <div class="gallery-swiper-btn-prev gallery-btn"><img src="/assets/images/arrow-blue-left.svg" alt=""></div>
            <div class="gallery-swiper-btn-next gallery-btn"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></div>
        </div>
    </div>
</div> --}}
