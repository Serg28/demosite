<div class="mobile-fixed-row-product">
    <div class="row flex v--center h--between pl-12 pr-12 pt-8 pb-8">
        <div class="price-wrap flex v--center">
            @if ($priceOld && ($priceOld>$price))
            <s class="fsz-12 color--gray mr-16">@money($priceOld) {{ setting('currency') }}</s>
            @endif
            <p class="color--red fsz-16 fw-600">@money($price) {{ setting('currency') }}</p>
        </div>
        <button class="main-btn blue-small icon-left"><span class="icon"><img src="/assets/images/cart-white.svg" alt=""></span>Купити</button>
    </div>
</div>
