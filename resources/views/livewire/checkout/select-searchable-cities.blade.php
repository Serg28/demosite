<div>
    <div x-data="{ defaultValue: '{{$defaultValue}}', value: @entangle('value')}" class="select-searchable">
        <div wire:ignore :class="value ? '' : 'error'">{{$value}}
            <select class="select-choices select2-cities" wire:model.live="value" wire:change="select($wire.value.value)">
                <option value="">{{$placeholder}}</option>
                <option value="2853">{{__t('Київ')}}</option>
                <option value="3855">{{__t('Львів')}}</option>
                <option value="6311">{{__t('Харків')}}</option>
                <option value="804">{{__t('Дніпро')}}</option>
                <option value="5033">{{__t('Полтава')}}</option>
            </select>
        </div>
    </div>
</div>

@assets
<link rel="stylesheet" href="{{mix('/assets/css/choices.min.css')}}">
@endassets

@script
    <script>
        window.choicesCities = SelectSearchable(window.lang + 'checkout/search/cities', 'select2-cities');
    </script>
@endscript
