<option value="">{{__cms('Выбрать раздел')}}</option>
@foreach ($data as $value => $caption)
	<option value="{{ $value }}" {{request('selected') == $value ? 'selected' : ''}}>{{ $caption }}</option>
@endforeach