<div class="imgInfoBox-relative-block">
    <label class="checkbox">
        <input type="checkbox" id="{{$fieldName}}" name="{{ $fieldName }}" value="1" @if ($entity->$fieldName) checked="checked" @endif>
        <i></i>{{__cms($field['caption'])}}
    </label>
</div>
