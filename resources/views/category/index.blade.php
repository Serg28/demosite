@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo')
@stop

@section('main')
    @include('partials.breadcrumb')

    <livewire:category.filter-products :page="$page"/>

    @livewire('reviews.slider.comments', ['class' => 'mb-80 mb-60-tablet mb-40-mob'])
    @include('partials.blocks.viewed_products') {{-- Готово --}}
    {{-- @include('partials.blocks.for_you') --}}
    @include('category.category.seo_text')
    <livewire:form.subscribe/>
@stop

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/catalog.min.css')}}">
@endpush
@push('footer-styles')
    {{--<link rel="stylesheet" href="{{mix('/assets/css/pages/home.css')}}"> --}}
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
@endpush
@push('footer-scripts')
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="/assets/js/swiper.js"></script>
@endpush