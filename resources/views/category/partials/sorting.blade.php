{{--
<a href="#" class="main-btn bg-green mob-visible get-filter"><img src="/img/filter.svg" alt="">{{__t('Фільтр')}}</a>
<div class="select-section d-flex ai-c">
    <p>{{__t('Сортировать')}}:</p>
    <select name="sort" id="" autocomplete="off" onchange="location.href=this.value">
        @foreach($filter->getSortingAll() as $value => $text)
            <option value="{{$filter->getUrlSort($value)}}"
                {{$filter->getFilterSort() == $value ? 'selected' : ''}}>{{__t($text)}}</option>
        @endforeach
    </select>
</div>
<div class="select-section d-flex ai-c">
    <p>{{__t('Показати')}}:</p>
    <select name="show" id="" autocomplete="off" onchange="location.href=this.value">
        @foreach($filter->getCountAll() as $key => $value)
        <option value="{{$value!==$filter->defaultShow() ? $filter->getUrlShowCount($value) : $filter->getUrlWithoutShow()}}"
            {{$filter->getFilterShow() == $value ? 'selected' : ''}}>{{$value}}</option>
        @endforeach
    </select>
</div>
--}}


<div class="sort-by flex v--center">
    <span class="fsz-16 color--gray">{{__t('Сортування')}}:</span>
    <div class="custom-select relative">
        <div class="visible flex v--center">
            <input type="text" readonly value="{{ __t($filter->getCurrentSortingText()) }}" style="width: auto;">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="4" viewBox="0 0 8 4" fill="none">
                    <path d="M0 6.99382e-07L8 0L4 4L0 6.99382e-07Z" fill="#222222"/>
                </svg>
            </div>
            <div class="hidden">
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