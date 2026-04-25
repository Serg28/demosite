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
                    <div class="credit-main">
                        <h3 class="fsz-34 fw-600 heading">{{__t('Умови кредитування')}}</h3>
                        <div class="credit-main__wrap flex v--center h--wrap mt-24">
                            @foreach($list->children as $item)

                                    <a href="{{$item->getUrl()}}" class="col flex fd--column v--center h--center">
                                        <span class="icon"><img src="{{$item->picture}}" alt="{{e($item->t('title'))}}" width="42" height="42"></span>
                                        <span class="fw-600 color--black mt-16 fsz-18">{{$item->t('title')}}</span>
                                        <span class="mt-8 fsz-12 color--gray">{{$item->t('short_description')}}</span>
                                    </a>

                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <livewire:form.subscribe />
@stop

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/credit.min.css')}}">
@endpush
