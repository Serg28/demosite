@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo')
@stop

@section('main')

    <div class="info-page">
        <div class="container">
            <div class="info-page__wrap flex v--start h--between">
                <livewire:partials.sidebarmenu />
                <div class="content pb-60">
                    <div class="universal">
                        <h2 class="fsz-34 fw-600 heading">{{$page->t('title')}}</h2>
                        @foreach($page->blocks as $block)
                            @if($block->is_active)
                                @includeIf('partials.blocks.' . $block->template, compact('block', 'page', 'loop'))
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <livewire:form.subscribe />
@stop
@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/template-universal.min.css')}}">
@endpush
@push('footer-styles')
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
@endpush
@push('footer-scripts')
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="/assets/js/swiper.js"></script>
@endpush
