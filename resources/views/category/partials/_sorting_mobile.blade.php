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
{{--
<div class="product_header_right">
    <div class="products_view">
        <a onclick="setView('grid')" class="shorting_icon grid @if ($filter->getViewType() == 'grid') active @endif"><i class="ti-view-grid"></i></a>
        <a onclick="setView('list')" class="shorting_icon list @if ($filter->getViewType() == 'list') active @endif"><i class="ti-layout-list-thumb"></i></a>
    </div>
</div>
<script>
    function setView(view) {
            $.ajax({
            url: "/change-products-view/" + view,
            method: 'GET'
        });
    }
</script>--}}
