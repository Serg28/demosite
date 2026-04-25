@php
    $style = 'left: 45px; top: 6px;';
    if(isset($spinner_style)){
        $style = 'left: calc(50% - 47px); top: 8px;';
    }
@endphp
@if($product->isActive())
    <button class="main-btn blue-small ml-56 btn-addtocart"
            type="button"
            data-id="{{ $product->id }}"
            data-count="1"
            data-options=""
            data-sum="{{ $product->getPrice() }}"
            @if(setting('checkbox_dynamic_remarketing_google'))
            data-dynamic="{{ setting('code_dynamic_remarketing_google') }}"
            @endif
            @if(setting('checkbox_google_analytics_four'))
            data-ga4-addtocart
            @endif>
        <span class="spinner" style="display:none; {{$style}}" ></span>
        {{__t('У кошик')}}
    </button>
@endif
