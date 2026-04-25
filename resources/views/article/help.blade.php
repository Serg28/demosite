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
                    <div class="help">
                        <h3 class="fsz-34 fw-600 heading">{{$page->t('title')}}</h3>
                        <livewire:form.faqs.application limit="10" />
                        <div class="help__bottom-wrap mt-16 p-24">
                            <p class="fsz-18 fw-600">{{__t('Не знайшли відповіді на своє питання?')}}</p>
                            <span class="fsz-14 color--gray">{{__t('Заповніть форму нижче або зв\'яжіться з нами')}}</span>
                            <livewire:form.request-application subject="{{__t('Написати нам')}}" />
                            @include('article.partials.contacts_us',['class' => 'info mt-40 p-24 flex fd--column'])
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <livewire:form.subscribe />
@stop

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/help.min.css')}}">
@endpush