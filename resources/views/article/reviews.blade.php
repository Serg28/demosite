@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo')
@stop

@section('main')
    <div class="info-page">
        <div class="container">
            <div class="info-page__wrap flex v--start h--between">
                <livewire:partials.sidebarmenu />
                <livewire:reviews.company.comments :model="$page"/>
            </div>
        </div>
    </div>
    {{-- Контейнеры для форм добавления комментариев --}}
    <div class="reviews_form" wire:replace.self></div>
    <div class="reviews_reply_form" wire:replace.self></div>
    {{-- /Контейнеры для форм добавления комментариев --}}
    <livewire:form.subscribe />
@stop
@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/reviews.min.css')}}">
@endpush
@push('footer-styles')
    {{--<link rel="stylesheet" href="{{mix('/assets/css/pages/reviews.css')}}"> --}}
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
@endpush
@push('footer-scripts')
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="/assets/js/swiper.js"></script>
@endpush
