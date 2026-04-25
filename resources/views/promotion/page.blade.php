@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo_promotions')
@stop

@section('main')
    @include('partials.breadcrumb')

    <div class="sale-page">
        <div class="container">
            {{--@if(isset($currentCategory)), {!! str_replace('[category]', $currentCategory->t('title'), __t('категория «[category]»')) !!}@endif--}}
            <h1 class="screen-name">{{$page->getSeoH1()}}</h1>
            <div class="button-block">
                @foreach($filters as $filterSlug => $filterName)
                    <button class='{{$filterSlug === request()->get('filter') || (!$filterSlug && !request()->get('filter'))? 'active' : ''}}'
                            onclick="location.href =  '{{$filterSlug ? $page->getUrl() . '?filter=' . $filterSlug : $page->getUrl()}}'"
                    >{{$filterName}}</button>
                @endforeach
            </div>

        </div>
    </div>

@stop