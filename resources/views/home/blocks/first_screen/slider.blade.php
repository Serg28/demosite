{{-- Баннер-слайдер первого экрана главной страницы --}}
<div class="title-swiper-wrapper">
    @if($sliderBlock)
        <div class="title-swiper swiper">
            <div class="swiper-wrapper">
                @foreach ($sliderBlock as $item)
                    <div class="swiper-slide">
                        <picture>
                            <source srcset="{{$item->t('picture_mobile')}}" media="(max-width: 768px)">
                            <img loading="lazy" src="{{$item->t('picture')}}" alt="{!! $item->t('title') !!}" class="bg">
                        </picture>
                        <div class="content">
                            <p class="fw-600 fsz-34 color--white heading">{!! $item->t('title') !!}</p>
                            <p class="color--white fw-400 fsz-16 sub-heading">{!! $item->t('title_dop') !!}</p>
                            <a href="{{$item->t('link')}}" class="main-btn blue-big">{{$item->t('link_title')}}</a>
                            <div class="info flex v--center">
                                {!! strip_tags($item->t('description')) !!}
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            <div class="swiper-pagination"></div>
            <div class="title-swiper-btn title-swiper-btn-prev"><img src="/assets/images/arrow-left-white.svg" alt=""></div>
            <div class="title-swiper-btn title-swiper-btn-next"><img src="/assets/images/arrow-right-white.svg" alt=""></div>
        </div>
    @endif
</div>
