@if(!empty($bestOffersProducts->item))
<div class="product-screen swiper-screen best-screen">
    <div class="container">
        <h2 class="fsz-28 fw-600 product-screen-heading">{{__t('Найкращі пропозиції')}}</h2>
        <div class="custom-swiper-wrapper">
            <div class="best-product-swiper swiper custom-swiper">
                <div class="swiper-wrapper">
                    @foreach($bestOffersProducts as $product)
                        <div class="swiper-slide"  wire:key="home_slider_tab_1_{{ $loop->index }}">
                            @include('partials.product')
                            {{--<livewire:product.card :product="$product" key="home_slider_tab_1_card_{{$loop->index}}" /> --}}
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination custom-pagiation"></div>
            </div>
            <div class="custom-swiper-btn custom-swiper-btn-prev best-product-swiper-btn-prev">
                <img src="assets/images/arrow-blue-left.svg" alt="">
            </div>
            <div class="custom-swiper-btn custom-swiper-btn-next best-product-swiper-btn-next">
                <img src="assets/images/arrow-blue-right-1.svg" alt="">
            </div>
        </div>
        <div class="button-block">
            <a href="{!! setting('blok-luchshie-predlozheniya-ssylka') !!}" class="show-all-prod  v--center fsz-18 fw-500 color--blue">{{__t('Дивитись всі')}} <img src="assets/images/arrow-blue-right.svg" alt=""></a>
        </div>
    </div>
</div>
@endif