
<div class="product-screen swiper-screen">
    <div class="container">
        <p class="product-screen-heading fsz-28 fw-600">{{ __t('Бестселлери') }}</p>
        <div class="scrl">
            <div class="tabs flex v--center product-tabs">
                <span href="" class="tab relative current color--gray fsz-16" data-swiper="1">{{ __t('Топ товари') }}</span>
                <span href="" class="tab relative color--gray fsz-16" data-swiper="2">{{ __t('Смартфони') }}</span>
            </div>
        </div>
        <div class="screens">
            <div class="screen screen-1">
                <div class="custom-swiper-wrapper">
                    <div class="product-swiper swiper custom-swiper">
                        <div class="swiper-wrapper">
                            @loop($hitsProducts as $product)
                                <div class="swiper-slide">
                                    @include('partials.product')
                                </div>
                            @endloop
                        </div>
                        <div class="swiper-pagination product-swiper-pagination custom-pagiation"></div>
                    </div>
                    <div class="custom-swiper-btn custom-swiper-btn-prev product-swiper-btn-prev"><img src="assets/images/arrow-blue-left.svg" alt=""></div>
                    <div class="custom-swiper-btn custom-swiper-btn-next product-swiper-btn-next"><img src="assets/images/arrow-blue-right-1.svg" alt=""></div>
                </div>
            </div>
            <div class="screen screen-2">
                <div class="custom-swiper-wrapper">
                    <div class="product-swiper swiper custom-swiper">
                        <div class="swiper-wrapper">
                            @loop($hitsProducts as $product)
                                <div class="swiper-slide">
                                    @include('partials.product')
                                </div>
                            @endloop
                        </div>
                        <div class="swiper-pagination product-swiper-pagination custom-pagiation"></div>
                    </div>
                    <div class="custom-swiper-btn custom-swiper-btn-prev product-swiper-btn-prev"><img src="assets/images/arrow-blue-left.svg" alt=""></div>
                    <div class="custom-swiper-btn custom-swiper-btn-next product-swiper-btn-next"><img src="assets/images/arrow-blue-right-1.svg" alt=""></div>
                </div>
            </div>
        </div>
    </div>
</div>