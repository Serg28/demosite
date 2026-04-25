<div>
    <div x-data="{ city_id: @entangle('cityId'), value: @entangle('value'), delivery_id: @entangle('deliveryId')}" class="select-searchable">
        <div wire:ignore :class="value ? '' : 'error'">
            <select class="select-choices select2-np-rozetka-{{md5($model)}}" wire:model.live="value"
                    wire:change="$parent.setProperty('{{$model}}',$wire.value.value)">
                <option value="">{{$placeholder}}</option>
            </select>
        </div>
    </div>
</div>

{{--
@push('footer-scripts')
    <link rel="stylesheet" href="/css/сhoices-custom.css">
@endpush

@assets
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
@endassets
--}}

@assets
<link rel="stylesheet" href="{{mix('/css/choices.css')}}">
@endassets

@script
<script>
    setTimeout(() => {
        const customRequestBody = {
            city: $wire.cityId,
            id: $wire.deliveryId,
            type: 'rozetka'
        };
        SelectSearchable(window.lang + 'checkout/delivery/all-pointers', 'select2-np-rozetka-{{md5($model)}}', {}, {}, customRequestBody);
    }, 300);
</script>
@endscript