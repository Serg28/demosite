<div class="product-screen swiper-screen">
    <div class="container">
        <h2 class="fsz-28 fw-600 heading" id="acses">{{ __t('Аксесуари') }}</h2>
        <div class="custom-swiper-wrapper">
            <div class="product-swiper swiper custom-swiper">
                <div class="swiper-wrapper">
                @include('product.partials.screen.slide')
                @include('product.partials.screen.slide')
                @include('product.partials.screen.slide')
                @include('product.partials.screen.slide')
                @include('product.partials.screen.slide')
                @include('product.partials.screen.slide')
                @include('product.partials.screen.slide')
                @include('product.partials.screen.slide')
                @include('product.partials.screen.slide')
                </div>
                <div class="swiper-pagination product-swiper-pagination custom-pagiation"></div>
            </div>
            <div class="custom-swiper-btn custom-swiper-btn-prev product-swiper-btn-prev"><img src="/assets/images/arrow-blue-left.svg" alt=""></div>
            <div class="custom-swiper-btn custom-swiper-btn-next product-swiper-btn-next"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></div>
        </div>
    </div>
</div>
