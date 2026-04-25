<section class="{{$field->getClassName()}}">
	<label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
	<div style="position: relative;">
		<div class="div_input">
			<div class="input_content">
				@if ($field->getOptions($definition))
				<label class="select">
					<select name="{{ $field->getNameField() }}" class="dblclick-edit-input form-control input-small unselectable {{ $field->getNameField() }}_foreign">
						<option value="">{{__cms('Выбрать менеджера')}}</option>
						@foreach ($field->getOptions($definition) as $value => $caption)
							<option value="{{ $value }}" {{$value == $managerId ? "selected" : ""}} >{{ $caption }}</option>
						@endforeach
					</select>
					<i></i>
				</label>
				@else
					<p>{{__cms('Менеджер не выбран')}}</p>
				@endif
			</div>
		</div>
	</div>
</section>
