<div class="mobile-sort relative">
    <div class="btn get-sort relative">
        <div class="row">
            <div class="visible flex v--center">
                <input type="text" readonly value="{{ __t($filter->getCurrentSortingText()) }}" style="width: auto;">
                <div class="sub-menu">
                    <ul class="flex fd--column">
                        @foreach($filter->getSortingAll() as $value => $text)
                            <li @click="window.location.href = '{{ $filter->getUrlSort($value) }}'">
                                <span class="select-row {{$filter->getFilterSort() === $value ? 'current' : ''}}">{{ __t($text) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>