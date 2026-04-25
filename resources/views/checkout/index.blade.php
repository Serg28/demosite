@extends('layouts.checkout')

@section('seo_tags')
    <title>{{__t('Оформлення замовлення')}}</title>
@stop

@section('internet_marketing_head')


@stop

@section('internet_marketing_facebook_pixel')

@stop

@section('internet_GA4_head')
    <script>
        dataLayer.push({ecommerce: null});
        dataLayer.push({
            'event': 'begin_checkout',
            'ecommerce': {
                'items': {!! $googleGA4Analytics !!}
            }
        });
    </script>
@stop

@section('main')

<div class="checkout pb-80">
    <livewire:checkout.checkout />
</div>

@stop

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/checkout.min.css')}}">
@endpush
@section('scripts')
    <script src="{{ mix('/assets/js/checkout.min.js') }}"></script>
@stop