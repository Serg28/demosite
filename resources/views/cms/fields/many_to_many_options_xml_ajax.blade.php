<select class="multiselect" multiple="multiple" id="{{$field}}" name="{{$field}}[]">
   @foreach ($characteristicOptions as $item)
      <option value="{{$item->id}}">{{ $item->t('title') }}</option>
   @endforeach
</select>

<script>
   $(".multiselect").multiselect();
</script>