@extends('layouts.default')

@section('seo_tags')
    @include('partials.seo')
@stop

@section('main')
    @include('partials.breadcrumb')
    <!-- END MAIN CONTENT -->


    <div class="page-catalog">
        <!-- START SECTION SHOP-->
        <div class="container">
            <h1 class="screen-name pb-50">{{$page->getSeoH1()}}</h1>
            {{--{{$rubrics}}--}}
            @if($rubrics)
                @php
                    $count = count($rubrics);
                @endphp
            @endif
            <div class="row pb-50">
                <div class="page-catalog-wrap col-4">
                    @foreach($rubrics as $category)

                        @if(!$category->treemenu)
                            <a href="{{$category->getUrl()}}"
                               @if($category->is_target_blank) target="_blank" @endif
                               class="cell col">
                                <img src="{!! $category->getImgPath('', 300) !!}" title="{{$category->t('title')}}"
                                     alt="{{$category->t('title')}}">
                                <h2 class="cell-name">{{$category->t('title')}}</h2>
                            </a>
                        @else
                            @if(count($category->children)>0)
                                @if($category->treemenu->menu_type=='App\\Models\\MenuSection')
                                    </div>
                                    <div class="category-name">
                                        @if($category->is_active)
                                            <a href="{{$category->getUrl()}}">{{$category->t('title')}}</a>
                                        @else
                                            {{$category->t('title')}}
                                        @endif
                                    </div>

                                    <div class="page-catalog-wrap col-4 pb-50">
                                @endif
                                @foreach($category->children as $rubric)
                                            <a href="{!! ($rubric->treemenu) ? $rubric->treemenu->getUrl() : $rubric->getUrl()!!}"
                                               @if($rubric->treemenu) {!! $rubric->treemenu->is_target_blank ? 'target="_blank"' : '' !!} @endif
                                               class="cell col">
                                                <img src="{!! $rubric->getImgPath('', 300) !!}" title="{{$rubric->t('title')}}"
                                                     alt="{{$rubric->t('title')}}">
                                                <h2 class="cell-name">{{$rubric->t('title')}}</h2>
                                            </a>
                                @endforeach
                                @if($category->treemenu->menu_type=='App\\Models\\MenuSection')
                                        <!--</div>-->
                                @endif
                            @else
                                    <a href="{!! ($category->treemenu) ? $category->treemenu->getUrl() : $category->getUrl()!!}"
                                       @if($category->treemenu) {!! $category->treemenu->is_target_blank ? 'target="_blank"' : '' !!} @endif
                                       class="cell col">
                                        <img src="{!! $category->getImgPath('', 300) !!}" title="{{$category->t('title')}}"
                                             alt="{{$category->t('title')}}">
                                        <h2 class="cell-name">{{$category->t('title')}}</h2>
                                    </a>
                            @endif
                        @endif

                    <!-- / 20 and more -->
                    @endforeach
                </div>
            </div>
        </div>
        <!-- END SECTION SHOP -->
    </div>
    @include('seocatalog.partials.seo_text')

@stop
