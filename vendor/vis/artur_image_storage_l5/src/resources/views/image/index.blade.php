@extends('admin::new.layouts.default')

@section('title')
    {{__cms($title)}}
@stop

@section('main')
    @include('image-storage::image.partials.content')
@stop
