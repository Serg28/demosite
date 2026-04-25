<div class="imgInfoBox-relative-block">
    <label class="input">
        <textarea rows="{{$field['rows'] or '3'}}"  id="{{$fieldName}}"  name="{{ $fieldName }}" class="custom-scroll imgInfoBox-textarea">{{$entity->$fieldName}}</textarea>
    </label>
</div>
