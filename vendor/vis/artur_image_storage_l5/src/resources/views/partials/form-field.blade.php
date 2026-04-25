<div class="tab-content padding-5">
    @if($field['type'] != 'checkbox')
        <label class="label" for="{{$fieldName}}">{{__cms($field['caption'])}}</label>
    @endif

    @include('image-storage::partials.fields_types.field_' . $field['type'])
</div>
