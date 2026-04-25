@php
    $seotext = $page->getSeoText();
@endphp
@if($seotext)
    <div class="seo-text-section" id="toup">
        <div class="container">
            <div class="seo-text-section__wrap" x-data="{ expanded: false }">
                <h2 class="fsz-24 fw-600 heading">{!! $page->getSeoTitle() !!}</h2>
                <div class="description hidden" x-show="expanded" x-collapse.min.200px :class="expanded ? '' : 'hidden'">
                    {!! $seotext !!}
                </div>
                <div class="read-more-wrap-btn" :class="expanded ? 'active' : ''">
                    <a href="#toup" class="read-more-btn read-more fw-500 fsz-18 flex v--center scroll-to-seo color--black" @click="expanded = ! expanded">
                        <span class="visible">{{__t('Читати повністю')}}</span>
                        <span class="hidden">{{__t('Приховати')}}</span>
                        <img loading="lazy" src="/assets/images/arrow-down-black.svg" alt="">
                    </a href="#toup">
                </div>
            </div>
        </div>
    </div>
@endif