@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo')
@stop

@section('main')

    <div class="info-page">
        <div class="container">
            <div class="info-page__wrap flex v--start h--between">
                <livewire:partials.sidebarmenu />
                <div class="content pb-60 service">
                    <h2 class="fsz-34 fw-600 heading">{{$page->t('title')}}</h2>
                    <div class="return form-content mt-24 p-24">
                        <livewire:form.service-application subject="{{__t('Заявка на обмін або повернення товару')}}" />
                        @include('article.partials.contacts_us',['class' => 'info mt-40 p-24 flex fd--column'])
                    </div>
                </div>
            </div>
        </div>
    </div>
    <livewire:form.subscribe />
@stop

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/return.min.css')}}">
@endpush