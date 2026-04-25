@php /** @var \App\Fields\SelectMultiple $field */ @endphp
<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="select">
                    <input type="hidden" value="" name="{{ $field->getNameField() }}[]">
                    <select multiple name="{{ $field->getNameField() }}[]"
                            class="dblclick-edit-input form-control input-small unselectable {{$field->getAction() ? "action" : ""}}" style="height: 100px">
                        @foreach ($field->getOptions() as $value => $caption)
                            <option value="{{ $value }}"{{ $field->isSelected($value) ? 'selected' : '' }}>
                                {{ $caption }}
                            </option>
                        @endforeach
                    </select>
                    <i aria-hidden="true"></i>
                </label>

                @if ($field->getComment())
                    <div class="note">
                        {{$field->getComment()}}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@if ($field->getActionSelect())
    <script>
        $('select[name={{ $field->getNameField() }}]').change(function () {

            if (!$(this).val()) {
                $('select[name={{ $field->getActionSelect() }}] option').show();
                return null;
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
