<div class="account-page__content">
    <div class="row flex v--end h--between mb-24 heading-flex-row">
        <h2 class="fsz-28 fw-600 content-heading">{{ __t('Список бажань') }}</h2>
        @include('partials.sorting')
        @include('partials.sorting_mobile')
    </div>

    @if(!empty($count))
        <div class="account-favorite flex v--stretch h--wrap">
            @foreach($products as $product)
                @include('partials.product', ['favorite' => true])
            @endforeach
        </div>
        @include('partials.paginate', ['items' => $results['products']])
    @else
        <div class="account-favorite-empty">
            <div class="top-row p-24 br--br-4 bg--white">
                <p class="fsz-18 fw-600">{{__t('Упс! Ваш список бажань пустий')}}</p>
                <p class="mt-16">{{__t('Наповніть його товарами, скориставшись каталогом')}}</p>
            </div>
            @if ($viewedProducts !== null && $viewedProducts->isNotEmpty())
                <h2 class="fsz-20 fw-600 mt-40">{{ __t('Ви преглядали') }}</h2>
                <div class="custom-swiper-wrapper mt-24">
                    <div class="viewed-swiper swiper custom-swiper">
                        <div class="swiper-wrapper">
                            @foreach($viewedProducts as $product)
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
    @endif
</div>

<script>
    window.onload = function() {
        var swiper10 = new Swiper(".viewed-swiper ", {
            slidesPerView: 2,
            spaceBetween: 12,
            navigation: {
                nextEl: ".viewed-swiper-btn-next",
                prevEl: ".viewed-swiper-btn-prev"
            },
            pagination: {
                el: ".swiper-pagination"
            },
            breakpoints: {
                1080: {
                    slidesPerView: 3,
                    spaceBetween: 16
                }
            }
        });
    }
</script>
