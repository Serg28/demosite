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
                    <div class="delivery">
                        <h2 class="fsz-34 fw-600 heading">{{$page->t('title')}}</h2>
                        @foreach($page->blocks as $block)
                            @if($block->is_active && in_array($block->template,['block_delivery_info']))
                                @include('partials.blocks.' . $block->template, compact('block', 'page', 'loop'))
                            @endif
                        @endforeach

                        <div class="delivery__bottom-row mt-40 flex fd--column">
                            @foreach($page->blocks as $block)
                                @if($block->is_active && !in_array($block->template,['block_delivery_info']))
                                    @include('partials.blocks.' . $block->template, compact('block', 'page', 'loop'))
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <livewire:form.subscribe />
@stop
@push('footer-styles')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/delivery.css')}}">
@endpush
