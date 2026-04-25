@extends('layouts.default')

@section('seo_tags')
    <title>{{ __t('Список бажань') }}</title>
@stop

@section('main')

    <div class="account-page">
        <div class="container">
            <div class="account-page__wrap flex v--start h--between">
                @include('profile.partials.sidebar',['sell' => 'favorites'])
                <livewire:favorite.content wire:key="profileFavoriteLists" />
            </div>
        </div>
    </div>


@stop

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/account-favorite.min.css')}}">
@endpush
@push('footer-styles')
    {{--<link rel="stylesheet" href="{{mix('/assets/css/pages/home.css')}}"> --}}
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
@endpush
@push('footer-scripts')
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="{{mix('/assets/js/swiper.js')}}"></script>
@endpush



