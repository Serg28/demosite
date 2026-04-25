@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo')
@stop

@section('main')
    <div class="info-page">
        <div class="container">
            <div class="info-page__wrap flex v--start h--between">
                <livewire:partials.sidebarmenu />

                <div class="content">
                    <div class="about-us">
                        @foreach($page->blocks as $block)
                            @if($block->is_active)
                                @include('partials.blocks.' . $block->template, compact('block', 'page', 'loop'))
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewire('reviews.slider.comments')
    <livewire:form.subscribe />
@stop
@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/about-us.min.css')}}">
@endpush
@push('footer-styles')
    {{--<link rel="stylesheet" href="{{mix('/assets/css/pages/about-us.css')}}">--}}
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
@endpush
@push('footer-scripts')
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="/assets/js/swiper.js"></script>
@endpush
