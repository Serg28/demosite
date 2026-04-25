
<section class="{{$field->getClassName()}}">
	<div class="tab-pane active">

		<ul class="nav nav-tabs tabs-pull-right">
			<label class="label pull-left" style="line-height: 32px;">{{$field->getName()}}</label>
			@foreach ($field->getLanguage() as $tab)
				<li class="{{$loop->first ? 'active' : ''}}">
					<a href="#{{$field->getNameFieldLangTab($definition, $tab)}}" class="tab_{{$tab->language}}" data-toggle="tab">{{$tab->language}}</a>
				</li>
			@endforeach
		</ul>

		<div class="tab-content padding-5">
			@foreach ($field->getLanguage() as $tab)
				<div class="tab-pane section_tab_{{$tab->language}} {{ $loop->first ? 'active' : '' }}" id="{{$field->getNameFieldLangTab($definition, $tab)}}">
					<div style="position: relative;">
						<label class="input">
							<input type="text"
								   @if ($field->isDisabled())
									   disabled="disabled"
								   @endif

								   @if ($field->getValue() && $field->getReadonlyForEdit())
									   readonly
								   @endif
								   value="{{$field->getValueLanguage($tab->language)}}"
								   name="{{ $field->getNameField()}}[{{$tab->language}}]"
								   placeholder=""
								   class="dblclick-edit-input form-control input-sm unselectable"
								   @if ($loop->first)
									   data-name-input="{{$definition->getNameDefinition().$field->getNameField()}}"
									@endif
							>

						</label>
						<div class="div_input">
							<div class="input_content">
								@if (setting('konvertirovat-cenu'))
									<p style="padding-top: 5px">Цена: {{setting('kurs')}}x{{$field->getValueLanguage($tab->language)}}={{$field->priceOnsite()}}грн.</p>
								@endif
							</div>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@include('admin::form.fields.partials.traslation')




