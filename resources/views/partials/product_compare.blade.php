@php
        $partPay = $product->part_pay == 1 && $product->privat_payparts_count > 0 && $product->quantity > 0;
        $monoPay = $product->mono == 1 && $product->mono_payparts_count > 0 && $product->quantity > 0;
        $price = $product->getPrice();
        $oldprice = $product->getPriceOld();
        $url = $product->getUrl();
        $cacheKey = $product->getCacheKey();
        $title = $product->t('title');
        $isPrivatParts = ($product->has_privat_payparts)?$product->privat_payparts_count:0;
        $isMonoParts = ($product->has_mono_payparts)?$product->mono_payparts_count:0;
        //$comments = $product->comments()->with(['user'])->active()->get();
        $rating = $product->rating;//$comments->sum('rating');
        $base_characteristics = $product->baseCharacteristics;
        $favBtnDell = isset($favorite)?true:false;
        $checkCompare = $product->checkCompare();
        $isActive = $product->isActive();
        $currency = setting('currency');
        $key = "product_compare_".$product->id;
@endphp
<div class="swiper-slide" data-id="{{$product->id}}" wire:key="product-card-compare-{{$product->id}}">
    <div class="col flex v--center h--between">
        <a href="{{$url}}" class="img flex v--center h--center">
            {!! $product->getImg(190, 190) !!}
        </a>
        <div class="right flex fd--column">
            <a href="{{$url}}" class="name color--black fsz-14">{{$title}}</a>
            <div class="bottom flex h--between v--end"  itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                <meta itemprop="price" content="{{ $price }}"/>
                <meta itemprop="url" content="{{ $url }}"/>
                @if($isActive)
                    <link itemprop="availability" href="https://schema.org/InStock"/>
                @else
                    <link itemprop="availability" href="https://schema.org/OutOfStock"/>
                @endif

                <div class="price-wrap ">
                    @if ($oldprice && ($oldprice>$price))
                        <s class="fsz-14 color--gray">{{$oldprice}} {{ $currency }}</s>
                    @endif
                    <p class="price fsz-18 fw-600">@money($price) {{ $currency }}</p>
                </div>
                @if($isActive)
                    {{-- TODO: сделать кнопку через JS --}}
                    @include('partials.product_btn-add-to-cart', ['product' => $product])
                @endif
            </div>
        </div>
    </div>
</div>
