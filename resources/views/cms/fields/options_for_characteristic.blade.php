@foreach ($options as $option)
	<option value="{{ $option->id }}" {{request('option') == $option->id ? 'selected' : ''}}>{{ $option->t('title') }}</option>
@endforeach