@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo')
@stop

@section('main')
    @include('partials.breadcrumb')
    @include('category.seocatalog.center')
    {{-- @include('category.partials.popular') --}}  {{--  TODO: либо вручную для каждой кат-рии, либо по алгориму? --}}
    {{-- @include('partials.blocks.bestsellers') --}} {{-- TODO: бестселлеры сео-категории --}}
    @include('partials.blocks.new_products') {{-- готово --}}
    @include('category.partials.advantages')
    @include('category.seocatalog.seo_text')
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