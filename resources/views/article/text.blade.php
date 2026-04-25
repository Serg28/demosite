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
                    <div class="policy text-block">
                        <h3 class="fsz-34 fw-600 heading">{!! $page->getSeoH1() !!}</h3>
                        <div class="policy__bottom-row mt-24 flex fd--column">
                            {!! $page->t('description') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <livewire:form.subscribe />
@stop
@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/policy.min.css')}}">
@endpush