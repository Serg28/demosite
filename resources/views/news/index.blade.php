@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo')
@stop

@section('main')
    @include('partials.breadcrumb')
    @include('news.partials.center')
    <livewire:form.subscribe />
@stop

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/blog.min.css')}}">
@endpush
