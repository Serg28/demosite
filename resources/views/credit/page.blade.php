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
                    <div class="credit-single">
                        <a href="/credit" class="back flex v--center color--blue fsz-18 fw-500"><img src="/assets/images/back-arrow.svg" alt="{{__t('Назад')}}">{{__t('Назад')}}</a>
                        <h3 class="fsz-34 fw-600 mt-24 heading">{{$page->t('title')}}</h3>
                        <span class="fsz-12 color--gray mt-8">{{$page->t('short_description')}}</span>
                        <p class="mt-24">{!! $page->t('description') !!}</p>

                        @foreach($page->blocks as $block)
                            @if($block->is_active)
                                @include('partials.blocks.' . $block->template, compact('block', 'page', 'loop'))
                            @endif
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
    <livewire:form.subscribe />
@stop

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/credit.min.css')}}" />
@endpush