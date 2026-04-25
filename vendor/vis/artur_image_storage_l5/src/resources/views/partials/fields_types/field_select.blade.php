<div class="imgInfoBox-relative-block">
    <label class="select state-success">
        <select name="{{$fieldName}}" class="dblclick-edit-input form-control input-small unselectable valid">
            @foreach($field['options'] as $optionVal => $optionName)
                <option value="{{$optionVal}}" {{ $entity->$fieldName == $optionVal ? "selected" : "" }} >{{$optionName}}</option>
            @endforeach
        </select>
        <i></i>
    </label>
</div>
