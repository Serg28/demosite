<section class="{{$field->getClassName()}}">
	<label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
	<div style="position: relative;">
		<div class="div_input">
			<div class="input_content">
				<label class="input">
					<input
							@if ($field->isDisabled())
							disabled="disabled"
							@endif

							@if ($field->getValue() && $field->getReadonlyForEdit())
							readonly
							@endif

							type="text"
							value="{{ $field->getValue() }}"
							name="{{ $field->getNameField() }}"
							placeholder="{{ $field->getPlaceholder() }}"
							class="dblclick-edit-input form-control input-sm unselectable"
							data-name-input="{{$definition->getNameDefinition().$field->getNameField()}}"
					/>

				</label>
			</div>
		</div>
		<div class="div_input">
			<div class="input_content">
				@if (setting('konvertirovat-cenu'))
					<p style="padding-top: 5px">Цена: {{setting('kurs')}}x{{$field->getValue()}}={{$field->priceOnsite()}}грн.</p>
				@endif
			</div>
		</div>
	</div>
</section>


