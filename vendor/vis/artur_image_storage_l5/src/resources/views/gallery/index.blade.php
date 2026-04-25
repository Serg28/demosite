@extends('admin::new.layouts.default')

@section('title')
    {{__cms($title)}}
@stop

@section('main')
    @include('image-storage::gallery.partials.content')
@stop
