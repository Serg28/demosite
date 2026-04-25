@php
    $selected_filters = array_keys($filter->getSelectedFilters());
    $currentQuery = http_build_query(request()->query());
    $currentQuery = $currentQuery ? '?' . $currentQuery : '';
@endphp

<div class="sale-menu">
    <div class="sale-menu-heading"><img src="/img/bi.svg" alt=""> {{__t('Категории')}}</div>

    <div class="hidden-block">
        @if($categories)
            <div class="category-block">
                <ul>
                    @foreach($categories as $category)
                        <li><a
                                @if(@$currentCategory->id!==$category->id)
                                    href="{{geturl($page->getUrl())}}/category={{$category->id}}"
                                @else
                                    class="active"
                                @endif
                            >
                                <img src="{{$category->ico}}" alt="{{$category->t('title')}}"><span>{{$category->t('title')}}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif


            @foreach($activeCharacteristics as $characteristicsItem)


            <div class="filter-section @if (count($results['optionsStart']) > 6) hidden @endif" style="border-bottom: none">
                <div class="filter-sub-heading"><img src="/img/a5.svg" alt="">{!! $characteristicsItem->t('title') !!}</div>
                <div class="filter-wrapper category-block">
                    <div class="filters_category top block_has_li has_label">

                        @php
                            $options = $characteristicsItem->optionsCacheFiltered($results['optionsStart']);
                        @endphp

                        @foreach ($options as $option)
                            <label for="{{$option->localizedSlug}}">
                                <input type="checkbox" name="checkbox" id="{{$option->localizedSlug}}" class="checkbox"
                                       {{$filter->isChecked($option) ? "checked" : ""}}
                                       @if(!isset($results['options'][$option->id])) disabled="disabled" @endif
                                       value="{{$option->id}}">
                                <a @if (isset($results['options'][$option->id])) href="{{$filter->urlFilter($option)}}{{$currentQuery}}"
                                   @else class="disabled" @endif >
                                    {{$option->t('title')}}
                                </a>
                            </label>
                        @endforeach

                    </div>
                    @if (count($options) > 6)
                        <div class="deployment-row ">
                            <a href="" class="d-flex ai-c open-filter"><span><img src="/img/a1.svg" alt=""></span> <b data-text1="Розгорнути"
                                                                                                                      data-text2="Згорнути"></b></a>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
    </div>
</div>
