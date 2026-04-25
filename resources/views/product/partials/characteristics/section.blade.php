@php
    $allCharacteristics = $page->all_characteristics;
@endphp
@if(!empty($allCharacteristics))
<div class="characteristics-section flex fd--column">
    @loop ($allCharacteristics as $groupTitle => $characteristics)
    <p class="fsz-18 fw-600">{{ $groupTitle }}</p>
        @loop ($characteristics as $characteristic)
            <div class="row flex v--start relative">
                <span class="fsz-16 color--gray p-4 relative">{{ $characteristic['title'] }}</span>
                <div class="right ml-auto p-4 relative">
                    @if(!empty($characteristic['link']))
                    <a href="{{$characteristic['link']}}" class="color--blue">{{ $characteristic['values'] }}</a>
                    @else
                    <p>{{ $characteristic['values'] }}</p>
                    @endif
                </div>
            </div>
        @endloop
    @endloop
</div>
@endif



