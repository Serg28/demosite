<section class="{{$field->getClassName()}}">
	<label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
	<div style="position: relative;">
		<div class="div_input">
			<div class="input_content ">

					<label class="select">
						<select
								name="{{ $field->getNameField() }}" class="dblclick-edit-input form-control input-small unselectable {{ $field->getNameField() }}_foreign">
							<option value="">Выберите характеристику</option>
						</select>
						<i></i>
					</label>
			</div>
		</div>
	</div>
</section>

<script>
	$('[name=characteristic_id]').change(function () {
		if ($(this).val() == '') {
		    $('[name=characteristic_option_id]').html('<option value="">Выберите характеристику</option>');
		} else {
            $.post( "/admin/get-option-characteristic/" + $(this).val(), function( data ) {
                $('[name=characteristic_option_id]').html( data );
            });
		}
    });

    if ($('[name=characteristic_id]').val() != '') {
        $.post( "/admin/get-option-characteristic/" + $('[name=characteristic_id]').val(),
            {
                option: '{{$field->getValue()}}'
			},
        function( data ) {
            $('[name=characteristic_option_id]').html( data );
        });
	}

</script>