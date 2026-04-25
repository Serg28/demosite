@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo_promotions')
@stop

@section('main')
    @include('partials.breadcrumb', ['withoutH1' => true])


    <div class="sale-screen ">
        <div class="container">
            <div class="flex-row">
                <div class="left big"><img src="{{ $page->getImgPath(773) }}" alt="{{ $page->t('title') }}" title="{{ $page->t('title') }}"></div>
                <div class="right big">
                    <div class="sale-heading">
                        <h2>{!! $page->edit('title') !!}</h2>
                        @if(!empty($page->date_start) && $page->date_start !== '0000-00-00 00:00:00')
                        <p>
                           {!! str_replace(['[start]', '[finish]'], [$page->formateDate($page->date_start), $page->formateDate($page->date_finish) ], __t('Акция действует с [start] по [finish]')) !!}
                        </p>
                        @endif
                    </div>
                    @if(!empty($page->date_start) && $page->date_start !== '0000-00-00 00:00:00')
                    <div class="sale-timer">
                        @if($page->time_started && $page->time_finished)
                            <p>{{__t('Акция завершилась')}}</p>
                        @elseif($page->time_started && !$page->time_finished)
                            <p>{{__t('До конца акции')}}</p>
                            <div class="timer-wrap" id="timer-text" data-timer-text="{{$page->formateTDate($page->date_finish)}}">
                                <div class="timer-column">
                                    <p><strong id="days">0</strong> {{__t('дней')}}</p>
                                </div>
                                <div class="timer-column">
                                    <p><strong id="hours">0</strong> {{__t('часов')}}</p>
                                </div>
                                <div class="timer-column">
                                    <p><strong id="minutes">6</strong> {{__t('минут')}}</p>
                                </div>
                                <div class="timer-column">
                                    <p><strong id="seconds">0</strong> {{__t('секунд')}}</p>
                                </div>
                            </div>
                        @else
                            <p>{{__t('До начала акции')}}</p>
                            <div class="timer-wrap" id="timer-text" data-timer-text="{{$page->formateTDate($page->date_start)}}">
                                <div class="timer-column">
                                    <p><strong id="days">0</strong> {{__t('дней')}}</p>
                                </div>
                                <div class="timer-column">
                                    <p><strong id="hours">0</strong> {{__t('часов')}}</p>
                                </div>
                                <div class="timer-column">
                                    <p><strong id="minutes">0</strong> {{__t('минут')}}</p>
                                </div>
                                <div class="timer-column">
                                    <p><strong id="seconds">0</strong> {{__t('секунд')}}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    @endif
                    <div class="sale-sub-heading">
                        @if($page->t('short_description'))
                        {!! $page->t('short_description') !!}
                        @endif
                        @if($page->t('description'))
                        <div class="deployment-row ">
                            <a href="" class="d-flex ai-c open-sale-text"><span><img src="/img/a1.svg" alt=""></span> <b>{{__t('Детальные условия акции')}}</b></a>
                        </div>
                        @endif

                            @if($page->t('description'))
                                <div class="hiden-wrap">
                                    {!! $page->t('description') !!}
                                </div>
                            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

        <div class="sale-page">
            <div class="container">
                <div class="section-heading">
                    <h3 class="screen-name">{{__t('Товари, що беруть участь в акції')}}@if(isset($currentCategory)), {!! str_replace('[category]', $currentCategory->t('title'), __t('категория «[category]»')) !!}@endif</h3>
                </div>
                <div id="content_ajax" class="sale-wrapper d-flex jc-sb">
                    @include('promotion.partials.center')
                </div>
            </div>
        </div>

@stop
@section('scripts')
    <script defer src="{{ mix('/js/promotions.min.js') }}"></script>
@stop
