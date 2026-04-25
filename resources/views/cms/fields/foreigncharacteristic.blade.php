<section class="{{$field->getClassName()}}">
	<label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
	<div style="position: relative;">
		<div class="div_input">
			<div class="input_content">

				@if ($field->getOptions($definition))
				<label class="select">
					<select
							name="{{ $field->getNameField() }}" class="dblclick-edit-input form-control input-small unselectable {{ $field->getNameField() }}_foreign">
						@if ($field->isNullAble())
							<option value="">{{ $field->getNullValue() ?: '...' }}</option>
						@endif

						@foreach ($field->getOptions($definition) as $value => $caption)
							<option value="{{ $value }}" {{$value == $field->getValue()? "selected" : ""}} >{{ $caption }}</option>
						@endforeach

					</select>
					<i></i>
				</label>
				@else
					<p>{{__cms('Не выбрана категория для товара или у категории товара не выбраны характеристики')}}</p>
				@endif

			</div>
		</div>
	</div>
</section>
