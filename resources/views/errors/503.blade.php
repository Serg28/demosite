{{--
@extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __($exception->getMessage() ?: 'Service Unavailable'))
--}}

@extends('layouts.default')

@section('seo_tags')
    <title>503</title>
@stop

@section('main')
    <div class="page-404 pt-100 pb-100">
        <div class="container">
            <div class="page-404__wrap text--center">
                <p class="fsz-102 fw-800  title-404 color--blue-dark lh-140">503</p>
                <p class="mt-40 fsz-32 fw-600 heading">{{__t('Ой! Сторінку, яку ви вимагали, не знайдено!')}}</p>
                <p class="mt-8">{{__t('Внутренняя ошибка сервера. Мы уже работаем над решением проблемы. Пожалуйста, попробуйте еще раз позже.')}}</p>
                <a href="{{getUrl('/')}}" class="main-btn border-big mt-40">{{__t('Повернутися на головну')}}</a>
            </div>
        </div>
    </div>
@stop

