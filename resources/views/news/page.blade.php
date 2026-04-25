@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo')
@stop

@section('main')
    @include('partials.breadcrumb')
    <div class="single-post" itemscope itemtype="https://schema.org/Article">
        @include('news.partials.page_center')
    </div>
    @include('news.partials.blog',['list' => $newsByTags, 'count' => count($newsByTags)])
    <livewire:form.subscribe />
@stop
@push('footer-scripts')
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="{{mix('/assets/js/swiper.js')}}"></script>
@endpush
@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/blog.min.css')}}">
@endpush

