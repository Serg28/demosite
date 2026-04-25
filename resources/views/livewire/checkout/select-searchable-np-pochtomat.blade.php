<div>
    <div x-data="{ city_id: @entangle('cityId'), value: @entangle('value')}" class="select-searchable">
        <div wire:ignore :class="value ? '' : 'error'">
            <select class="select-choices select2-np-pochtomat-{{md5($model)}}" wire:model.live="value"
                    wire:change="$parent.setProperty('{{$model}}',$wire.value.value)">
                <option value="">{{$placeholder}}</option>
            </select>
        </div>
    </div>
</div>

@assets
<link rel="stylesheet" href="{{mix('/assets/css/choices.min.css')}}">
@endassets

@script
<script>
    setTimeout(() => {
        const customRequestBody = {
            city: $wire.cityId,
            type: 'np_pochtomat'
        };
        SelectSearchable(window.lang + 'checkout/delivery/pointers-np', 'select2-np-pochtomat-{{md5($model)}}', {}, {}, customRequestBody);
    }, 300);
</script>
@endscript