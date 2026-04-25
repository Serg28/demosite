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

                            if (item.price != undefined) {
                                $('[data-name-input=order_productsprice]').val(item.price);
                                $('[data-name-input=order_productsbase_price]').val(item.price);
                                $('[data-name-input=order_productsbase_amount]').val(item.price);
                                $('[data-name-input=order_productstotal_amount]').val(item.price);
							}

                            if (item.quantity && item.quantity != undefined) {

                                $('#create_form_order_products [name=count]').attr('data-max-value', parseInt(item.quantity));

                                // Получаем элементы формы и значения минимального и максимального диапазонов
                                const $input = $('#create_form_order_products [name=count]');


                                // Обрабатываем событие ввода в поле
                                $input.on('input enter change', function() {
                                    const minValue = parseInt(1);
                                    const maxValue = parseInt($input.attr('data-max-value'));
                                    // Получаем введенное значение и преобразуем его в число
                                    let value = parseInt($(this).val());
                                    $(this).val(Math.min(Math.max(value, minValue), maxValue));
                                });
							}

                            $('#create_form_order_products [name=count]').val(1);
                            $('#create_form_order_products [data-name-input=order_productsdiscount]').val(0);

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

<script>
    {{--
    $('[data-name-input=order_productsdiscount]').change(function() {
        var base_price = $('[data-name-input=order_productsbase_price]').val();
        var discount = $(this).val();
        var price = Math.ceil(base_price - (( base_price * discount ) /100));
        $('[data-name-input=order_productsprice]').val(price);
    })--}}

    $('#edit_form_order_products, #create_form_order_products').find('input').change(function() {
        const container = $(this).closest('#edit_form_order_products, #create_form_order_products');

        const base_price = parseInt(container.find('[data-name-input=order_productsbase_price]').val())  || 0;
        const discount = parseInt(container.find('[data-name-input=order_productsdiscount]').val())  || 0;
        let count = parseInt(container.find('[name=count]').val()) || 0;
        count = isNaN(count) || count < 1 ? 1 : count;

        const base_amount = count * base_price;
        const discount_amount = Math.ceil(base_amount * discount / 100);
        const total_amount = base_amount - discount_amount;
        const price = parseFloat((total_amount / count).toFixed(2)) || 0;

        container.find('[name=count]').val(count);
        container.find('[data-name-input=order_productsprice]').val(price);
        container.find('[data-name-input=order_productsdiscount_amount]').val(discount_amount);
        container.find('[data-name-input=order_productsbase_amount]').val(base_amount);
        container.find('[data-name-input=order_productstotal_amount]').val(total_amount);
    })

</script>
