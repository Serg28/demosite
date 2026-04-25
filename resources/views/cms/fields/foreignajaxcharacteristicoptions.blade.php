<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="input">
                    <input value="" type="text" name="{{ $field->getNameField() }}" class="form-control input-sm unselectable {{ $field->getNameField() }}_foreign">
                </label>
                @if ($field->getValue())
                    <div style="padding-top: 4px"><a onclick="deleteForeing{{$field->getNameField()}}()">{{__cms('Удалить')}}</a></div>
                @endif
                <script>

                    window.term = '';

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

                                window.term = term;

                                return {
                                    q: term,
                                    limit: 20,
                                    page: page,
                                    ident: '{!! $field->getNameField() !!}',
                                    query_type: 'foreign_ajax_search',
                                    characteristic_id: $('[name=characteristic_id]').find(":selected").val()
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
                            return 'По результату поиска ничего не найдено <a onclick="addNewValue()">Добавить новое значение</a>';
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

                    function addNewValue() {

                        jQuery.ajax({
                            type: "POST",
                            url: '/admin/characteristics/add-new-option',
                            data: {
                                "characteristic_id" : $('[name=characteristic_id]').find(":selected").val(),
                                "option" : window.term
                            },
                            dataType: 'json',
                            success: function (data) {
                                $select2{{$field->getNameField()}}.select2("data", data);
                                $('.select2-drop').hide();
                            },
                            error: function (xhr, ajaxOptions, thrownError) {

                            }
                        });
                    }

                </script>
            </div>
        </div>
    </div>
</section>
