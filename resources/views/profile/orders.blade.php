@extends('layouts.default')

@section('seo_tags')
    <title>{{ __t('Ваші замовлення') }}</title>
@stop

@section('main')

    <div class="account-page">
        <div class="container">
            <div class="account-page__wrap flex v--start h--between">
                @include('profile.partials.sidebar',['sell' => 'orders'])
                <livewire:profile.order.lists />
            </div>
        </div>
    </div>


@stop

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/account-history.min.css')}}">
@endpush
@push('footer-styles')
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
@endpush
@push('footer-scripts')
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="/assets/js/swiper.js"></script>
@endpush