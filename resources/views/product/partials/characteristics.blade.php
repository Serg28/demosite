@php
    $dop = [];
@endphp

@if(!empty($allCharacteristics))
    <div class="prod-characteristics mt-140" id="characteristics">
        <div class="container">
            <div class="prod-characteristics__wrap p-24">
                <h2 class="fsz-28 fw-600 text--center heading" id="characteristic">{{__t('Характеристики')}}</h2>
                <div class="prod-characteristics__rows flex fd--column hidden">
                    @loop ($allCharacteristics as $groupTitle => $characteristics)
                        <div class="characteristics-section flex fd--column">
                            @if($groupTitle!=='_no_group')<p class="fsz-18 fw-600">{{ $groupTitle }}</p>@endif
                            @loop ($characteristics as $characteristic)
                                @if(!empty($characteristic['characteristic_id']) && $characteristic['characteristic_id'] == setting('id-harakteristiki-dlya-snosky') )
                                    @php
                                        $dop[] = $characteristic['values'];
                                    @endphp
                                @else
                                    <div class="row flex v--start relative">
                                        <span class="fsz-16 color--gray p-4 relative">{{ $characteristic['title'] ?: '–' }}</span>
                                        <div class="right ml-auto p-4 relative">
                                            @if(!empty($characteristic['link']))
                                                <a href="{{$characteristic['link']}}" class="color--blue">{!! $characteristic['values'] !!}</a>
                                            @else
                                                <p>{!! $characteristic['values'] !!}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endloop
                        </div>
                    @endloop
                    <div class="info pl-4">
                        @if(!empty($dop))
                            @loop ($dop as $str)
                                <p class="fsz-13 color--gray">* {!! strip_tags($str)  !!}</p>
                            @endloop
                        @endif
                    </div>
                </div>
                <div class="read-more-wrap-btn">
                    <div class="more-char-btn more-char  fsz-18  v--center"><span class="visible">{{__t('Показати всі')}}</span><span class="hidden">{{__t('Приховати')}}</span><img src="/assets/images/arrow-down-blue.svg" alt=""></div>
                </div>
            </div>
        </div>
    </div>

    @endif
