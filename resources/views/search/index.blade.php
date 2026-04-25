@extends('layouts.default')

@section('seo_tags')
    <title>{{__t('Результати пошуку')}}</title>
@stop

@section('main')
    @include('partials.breadcrumb_simple',['page' => 'Результати пошуку'])

    @livewire('search.page')

    @livewire('reviews.slider.comments', ['class' => 'mb-80 mb-60-tablet mb-40-mob'])
    @include('partials.blocks.viewed_products')

    {{--@include('partials.blocks.for_you', ['class' => 'mt-40 pb-40']) --}}
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