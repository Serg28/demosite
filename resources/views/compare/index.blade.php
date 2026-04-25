@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo')
@stop

@section('main')
	@include('partials.breadcrumb_simple', ['page' => __t('Порівняння товарів')])
    <livewire:compare.content />

    <livewire:form.subscribe />
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Livewire.hook('message.processed', (message, component) => {
                initCompareSlider(); // Вызываем вашу функцию после обновления Livewire-компонента
            });
        });
    </script>
@stop
@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/comparison.min.css')}}">
@endpush
@push('footer-styles')
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
@endpush
@push('footer-scripts')
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="/assets/js/swiper.js"></script>
@endpush

