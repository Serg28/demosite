<div>
    <div x-data="{ city_id: @entangle('cityId'), defaultValue: '{{$defaultValue}}', value: @entangle('value')}" class="select-searchable">
        <div wire:ignore :class="value ? '' : 'error'">
            <select class="select-choices select2-searchable-pickup-{{md5($model)}}" wire:model.live="value"
                    wire:change="$parent.setProperty('{{$model}}',$wire.value.value)">
                <option value="">{{$placeholder}}</option>
            </select>
        </div>
    </div>
</div>

{{--
@push('footer-scripts')
    <link rel="stylesheet" href="/css/choices-custom.css">
@endpush
@assets
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endassets --}}

@assets
<link rel="stylesheet" href="{{mix('/assets/css/choices.min.css')}}">
@endassets

@script
<script>
    setTimeout(() => {
        const customRequestBody = {
            city: $wire.cityId,
        };
        const options = {
            preloadOnDropOpen: true,
        };
        SelectSearchable(window.lang + 'checkout/delivery/pointers', 'select2-searchable-pickup-{{md5($model)}}', options, {}, customRequestBody);
    }, 300);
</script>
@endscript
