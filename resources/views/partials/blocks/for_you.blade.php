{{-- TODO: блок Спеціально для вас ми підібрали --}}
<div class="product-screen swiper-screen new-screen not-padding {{$class ?? ''}}">
    <div class="container">
        <h2 class="fsz-28 fw-600 heading">{{__t('Спеціально для вас ми підібрали')}}</h2>
        <div class="custom-swiper-wrapper">
            <div class="new-product-swiper swiper custom-swiper">
                <div class="swiper-wrapper">
                    @include('partials.product_item_tmp')
                    @include('partials.product_item_tmp')
                    @include('partials.product_item_tmp')
                    @include('partials.product_item_tmp')
                    @include('partials.product_item_tmp')
                    @include('partials.product_item_tmp')
                    @include('partials.product_item_tmp')
                    @include('partials.product_item_tmp')
                </div>
                <div class="swiper-pagination custom-pagiation"></div>
            </div>
            <div class="custom-swiper-btn custom-swiper-btn-prev new-product-swiper-btn-prev">
                <img src="assets/images/arrow-blue-left.svg" alt="">
            </div>
            <div class="custom-swiper-btn custom-swiper-btn-next new-product-swiper-btn-next">
                <img src="assets/images/arrow-blue-right-1.svg" alt="">
            </div>
        </div>
    </div>
</div>