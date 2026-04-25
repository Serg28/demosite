@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo')
@stop

@section('main')
    @include('home.blocks.first_screen')
    @include('home.blocks.bestsellers')
    @includeWhen($whyWe,'home.blocks.block_home_why_we', compact('whyWe', 'page', 'blocks') )  {{-- готово --}}

    @include('product.blocks.best_offers_product')
    @include('partials.blocks.new_products', ['class' => 'pt-0']) {{-- готово --}}
    @livewire('reviews.slider.comments')

    @includeWhen($lastNews, 'home.blocks.last_news', compact('lastNews', 'page', 'blocks'))  {{-- готово --}}

    @include('home.blocks.seo')
    <livewire:form.subscribe/>
@stop
@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/home.min.css')}}">
@endpush
@push('footer-styles')
    {{--<link rel="stylesheet" href="{{mix('/assets/css/pages/home.css')}}"> --}}
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
@endpush
@push('footer-scripts')
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="{{mix('/assets/js/swiper.js')}}"></script>
@endpush
