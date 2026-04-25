<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}" style="display: inline-block">{{$field->getName()}}</label> @if ($field->getValue())
        <span style="margin-left: 4px; display: inline-block"><a onclick="deleteForeing{{$field->getNameField()}}()">{{__cms('Удалить')}}</a></span>
    @endif
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="input">
                    <input value=""
                           type="text" name="{{ $field->getNameField() }}"
                           class="form-control input-sm unselectable {{ $field->getNameField() }}_foreign"
                           @if ($field->isSaveOnChange() && request('id'))
                               onchange="TableBuilder.doSaveOnChange($(this), '{{request('id')}}')"
                           @endif
                    >
                </label>

                <script>
                    var $select2{{$field->getNameField()}} = $('.{{$field->getNameField()}}_foreign').select2({
                        placeholder: "Поиск",
                        minimumInputLength: {{ $search['minimum_length'] ?? '1' }},
                        language: "ru",
                        ajax: {
                            url: $('.{{$field->getNameField()}}_foreign').parents('form').attr('action'),
                            dataType: 'json',
                            type: 'POST',
                            quietMillis: 350,
                            data: function (term, page) {
                                return {
                                    q: term,
                                    limit: 20,
                                    page: page,
                                    id: '{{request('id')}}',
                                    cityId: $('[name="city_id"]').val(),
                                    ident: '{!! $field->getNameField() !!}',
                                    query_type: 'foreign_ajax_search',
                                };
                            },
                            results: function (data, page) {

                                return data;
                            }
                        },
                        formatResult: function(item) {
                            return item.name;
                        },
                        formatSelection: function(item) {
                            return item.name;
                        },
                        formatNoMatches : function () {
                            return 'По результату поиска ничего не найдено';
                        },
                        formatSearching: function () { return "Ищет..."; },
                        formatInputTooShort: function (input, min) { var n = min - input.length; return "Введите еще " + n + "   символ "; },

                        dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
                        escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
                    });

                    @if ($field->getValue())
                    $select2{{$field->getNameField()}}.select2("data", {!! json_encode($field->getValueForInput($definition)) !!});
                    @endif

                    function deleteForeing{{$field->getNameField()}}() {
                        $select2{{$field->getNameField()}}.select2("data", '');
                    }

                </script>
            </div>
        </div>
    </div>
</section>
