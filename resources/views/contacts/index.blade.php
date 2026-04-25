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
                    <div class="contacts">
                        <h3 class="fsz-34 fw-600 heading">{{__t('Контакти')}}</h3>
                        @foreach($page->blocks as $block)
                            @if($block->is_active && in_array($block->template,['contact_rubrics']))
                                @include('partials.blocks.' . $block->template, compact('block', 'page', 'loop'))
                            @endif
                        @endforeach
{{--                        @include('contacts.partials.contacts')--}}
                        <livewire:form.feedback subject="{{__t('Написати нам')}}" />
                    </div>
                </div>
            </div>
            <div class="contacts-adress-wrap">
                <div class="bottom-row flex v--stretch h--between">
                    @include('contacts.partials.adress')
                    <div class="right screen screen-2">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2653.8268292252246!2d30.525045625522228!3d50.43526513326473!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40d4cf7dfc584a03%3A0xc667cfe6b316e3f1!2sSmartMAG!5e0!3m2!1suk!2sua!4v1702305419525!5m2!1suk!2sua" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <livewire:form.subscribe />
@stop

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/contacts.min.css')}}">
@endpush