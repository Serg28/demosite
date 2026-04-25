@extends('admin::new.layouts.default')

@section('title')
    {{__cms($title)}}
@stop

@section('main')
    @include('image-storage::tag.partials.content')
@stop
