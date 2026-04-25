@if($points)
<p class="mast d-flex">{{__t('Отделение')}} <span>*</span></p>
<div class="delivery_points_list select-wrap">
    <select name="ukrposhta_warehouse_id" id="" class="title select2-points" data-placeholder="{{__t('Выберите отделение')}}">
        <option value="" selected disabled>{{__t('Выберите отделение')}}
        </option>
        @foreach($points as $point)
            <option value="{{$point['id']}}">{{$point['title']}}
            </option>
        @endforeach
    </select>
</div>
@endif
