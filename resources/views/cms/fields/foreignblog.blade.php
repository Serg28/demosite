<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="select">
                    <select name="{{ $field->getNameField() }}" class="dblclick-edit-input form-control input-small unselectable {{ $field->getNameField() }}_foreign">
                        <option value="">Выбрать автора</option>
                        @foreach ($field->getOptions($definition) as $value => $caption)
                            <option value="{{ $value }}" {{$value == $field->getValue()? "selected" : ""}} >{{ $caption }}</option>
                        @endforeach
                    </select>
                    <i aria-hidden="true"></i>
                </label>
            </div>
        </div>
    </div>
</section>


