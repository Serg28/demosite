@extends('layouts.default')

@section('seo_tags')
    <title>{{__t('Активація користувача')}}</title>
@stop

@section('main')

    @include('partials.breadcrumb_simple', ['page' => __t('Активація користувача')])

    <div class="page-404 pt-100 pb-100">
        <div class="container">
            <div class="page-404__wrap text--center">
                <p class="mt-40 fsz-32 fw-600 heading">{!! $result !!}</p>
                <p class="mt-8">{!! $description !!}</p>
                <a href="{{getUrl('/')}}" class="main-btn border-big mt-40">{{__t('Повернутися на головну')}}</a>
            </div>
        </div>
    </div>

@stop
