<div class="product-screen swiper-screen best-screen {{$class ?? ''}} ">
    <div class="container">
    @if($similarProducts->isNotEmpty())
        <h2 class="fsz-28 fw-600 heading" id="similar">{{ __t('Вам також може сподобатися') }}</h2>
        <div class="custom-swiper-wrapper">
            <div class="best-product-swiper swiper custom-swiper">
                <div class="swiper-wrapper">
                    @foreach($similarProducts as $product)
                            <div class="swiper-slide">
                                @include('partials.product')
                            </div>
                    @endforeach
                </div>
                <div class="swiper-pagination custom-pagiation"></div>
            </div>
            <div class="custom-swiper-btn custom-swiper-btn-prev new-product-swiper-btn-prev">
                <img src="/assets/images/arrow-blue-left.svg" alt="">
            </div>
            <div class="custom-swiper-btn custom-swiper-btn-next new-product-swiper-btn-next">
                <img src="/assets/images/arrow-blue-right-1.svg" alt="">
            </div>
        </div>
    @endif
    </div>
</div>