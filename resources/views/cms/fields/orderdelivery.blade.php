<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">

                <label class="select">
                    <select
                        {{request("id") && $field->getReadonlyForEdit() ? 'disabled' : ''}}
                        @if ($field->isSaveOnChange() && request('id'))
                            onchange="TableBuilder.doSaveOnChange($(this), '{{request('id')}}')"
                        @endif

                        name="{{ $field->getNameField() }}" class="dblclick-edit-input form-control input-small unselectable {{ $field->getNameField() }}_foreign {{$field->getNameFieldWithDefinition($definition)}}">
                        @if ($field->isNullAble())
                            <option value="">{{ $field->getNullValue() ?: '...' }}</option>
                        @endif

                        @foreach ($field->getOptions($definition) as $value => $caption)
                            <option value="{{ $value }}" {{$value == $field->getValue()? "selected" : ""}} >{{ $caption }}</option>
                        @endforeach

                    </select>
                    <i></i>
                </label>

            </div>
        </div>
    </div>
</section>

@if ($field->getActionSelect())

    <script>
        $('select[name={{ $field->getNameField() }}]').change(function () {

            if (!$(this).val()) {
                $('select[name={{ $field->getActionSelect() }}] option').show();
                return;
            }

            $('select[name={{ $field->getActionSelect() }}] option').hide();
            $('select[name={{ $field->getActionSelect() }}] option[data-class=' + $(this).val() + ']').show();
            $('select[name={{ $field->getActionSelect() }}] option[value=""').show();
            $('select[name={{ $field->getActionSelect() }}]').prop("selected", true).val('').change();
        });

        if ($('select[name={{ $field->getNameField() }}]').val()) {
            $('select[name={{ $field->getActionSelect() }}] option').hide();
            $('select[name={{ $field->getActionSelect() }}] option[data-class=' + $('select[name={{ $field->getNameField() }}]').val() + ']').show();
            $('select[name={{ $field->getActionSelect() }}] option[value=""').show();
        }

    </script>
@endif

@if($field->isWarehouseResetFieldsOnChange())
    <script>
        $('select[name={{ $field->getNameField() }}]').change(function () {
            $('.warehouse_field').select2("data", '').val(null).trigger('change');
        });
    </script>
@endif

@if ($field->getAction())

    <script>

        var selectClass = '{{$field->getNameFieldWithDefinition($definition)}}';
        var formClass = 'modal_form_{{$definition->model()->getTable()}}';

        $('.' + formClass + ' select.' + selectClass).change(function () {
            $("." + formClass + " section.section_field").hide();
            $("." + formClass + " section.section_field." + $(this).val()).show();
        });

        $("." + formClass + " section.section_field").hide();

        if ($('.' + formClass + ' select.' + selectClass).val()) {
            $("." + formClass + " section.section_field." + $('select.' + selectClass).val()).show();
        }

    </script>

@endif
