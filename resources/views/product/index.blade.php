@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo')
@stop

@section('internet_GA4_head')
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ecommerce: null});
        dataLayer.push({
            'event': "view_item",
            'ecommerce': {
                'items': [{
                    'item_name': "{{$page->t('title')}}",
                    'item_id': "{{$page->getArticle()}}",
                    'price': {{ $page->getPrice() }},
                    'item_brand': "{{ $page->getBrandName() }}",
                    'item_category': "{{$page->getTopCategoryName()}}",
                    'item_category_2': "{{$page->getParentCategoryName()}}",
                    'item_list_name': "{{ $page->getListName() }}"
                }]
            }
        });
    </script>

    <meta name="data-title" content="{{$page->t('title')}}">
    <meta name="data-code" content="{{$page->getArticle()}}">
    <meta name="data-category2" content="{{$page->getParentCategoryName()}}">
    <meta name="data-category" content="{{$page->getTopCategoryName()}}">
    <meta name="data-brand" content="{{ $page->getBrandName() }}">
    <meta name="data-price" content="{{ $page->getPrice() }}">
    <meta name="data-list-name" content="{{ $page->getListName() }}">
@stop

@section('main')
    @include('partials.breadcrumb')
    @include('product.partials.content')
    @include('product.partials.similar_products')
    @include('product.partials.characteristics')
    @include('product.partials.description')
    @include('product.partials.reviews')
    {{--@include('product.partials.screen.accessories') --}}


    @livewire('blocks.viewed-products', ['class' => 'mb--120', 'lazy' => true])


    <div class="main-popup-wrap product-gallery-popup">
        <div class="popup-wrap">
            <div class="popup-close"></div>
            <div class="popup">
                <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
                <div class="heading-row relative">
                    <h2 class="fsz-24 fw-600">{{$page->t('title')}}</h2>
                </div>
                <div class="galerry-swiper-wrapper flex v--center h--between">
                    <div class="swiper gallery-thumbs-1">
                        <div class="swiper-wrapper">
                            @if(!empty($page->picture))
                                <div class="swiper-slide">
                                    <img loading="lazy" src="{{$page->getImgPath(40, 40)}}" alt="{{ e($page->t('title')) }}">
                                </div>
                            @endif
                            @if(!empty($otherPictures))
                                @foreach($otherPictures as $pictureOriginal => $pictureSmall)
                                    <div class="swiper-slide">
                                        <img loading="lazy" src="{{glide($pictureOriginal, ['w' => 40, 'h' => 40])}}" alt="{{ e($page->t('title')) }}  {{__t('фото')}} №{{$loop->index}}">
                                    </div>
                                @endforeach
                            @endif
                            @if(empty($page->picture) && empty($otherPictures))
                                <div class="swiper-slide">
                                    <img loading="lazy" src="{!! glide($page->firstOtherPicture, ['w'=>40, 'h'=>40]) !!}" alt="{{e($page->t('title'))}}">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="gallery-swiper-1 swiper">
                        <div class="swiper-wrapper">
                            @if(!empty($page->picture))
                                <div class="swiper-slide">
                                    <img loading="lazy" src="{{$page->getImgPath('', 820)}}" alt="{{ e($page->t('title')) }}">
                                </div>
                            @endif
                            @if(isset($otherPictures))
                                @foreach($otherPictures as $pictureOriginal => $pictureSmall)
                                    <div class="swiper-slide">
                                        <img loading="lazy" src="{{glide($pictureOriginal, ['w' => '', 'h' => 820])}}" alt="{{ e($page->t('title')) }}  {{__t('фото')}} №{{$loop->index}}">
                                    </div>
                                @endforeach
                            @endif
                            @if(empty($page->picture) && !empty($otherPictures))
                                <div class="swiper-slide">
                                    <img loading="lazy" src="{!! glide($page->firstOtherPicture, ['w'=>'', 'h'=>820]) !!}" alt="{{e($page->t('title'))}}">
                                </div>
                            @endif
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="gallery-swiper-btn-prev gallery-btn"><img src="/assets/images/arrow-blue-left.svg" alt=""></div>
                        <div class="gallery-swiper-btn-next gallery-btn"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></div>
                    </div>
                </div>
                <div class="center-block flex v--center h--center">
                    <div class="price-wrap flex v--center">
                        @if ($priceOld && ($priceOld>$price))
                            <s class="fsz-18 color--gray">@money($priceOld) {{ setting('currency') }}</s>
                        @endif
                        <p class="color--red fsz-20 fw-600">@money($price) {{ setting('currency') }}</p>
                    </div>
                    @include('partials.product-btn-add-to-cart', ['product' => $page, 'spinner_style' => true])
                </div>
            </div>
        </div>
    </div>


    <div class="mt-140">
        <livewire:form.subscribe/>
    </div>

    @include('product.partials.mobile-fixed')
    @include('product.partials.change')
@stop

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/product.min.css')}}">
@endpush
@push('footer-styles')
    {{--<link rel="stylesheet" href="{{mix('/assets/css/pages/home.css')}}"> --}}
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
@endpush
@push('footer-scripts')
    <script defer src="{{mix('/assets/js/product.min.js')}}" data-navigate-track></script>
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="{{mix('/assets/js/swiper.min.js')}}"></script>
@endpush