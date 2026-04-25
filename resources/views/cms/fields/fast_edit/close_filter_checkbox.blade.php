<span class="onoffswitch">
	<input onchange="activeToggle('{{$idRecord}}', this.checked, '{{$field}}');" type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" @if ($isChecked) checked="checked" @endif id="myonoffswitch{{$idRecord}}">
	<label class="onoffswitch-label" for="myonoffswitch{{$idRecord}}">
		<span class="onoffswitch-inner" data-swchon-text="{{__cms('ДА')}}" data-swchoff-text="{{__cms("НЕТ")}}"></span>
		<span class="onoffswitch-switch"></span>
	</label>
</span>

<script>
    function activeToggle(id, isActive, field)
    {
        isActive = isActive ? 1 : 0;

        jQuery.ajax({
            url: '/admin/category_characteristics',
            type: 'POST',
            //dataType: 'json',
            cache: false,
            data: {
                pk: id,
                value: isActive,
                ident: field,
                query_type: 'do_fast_change_field'
            },
            success: function(response) {
            },
            error: function (xhr, ajaxOptions, thrownError) {
                var errorResult = jQuery.parseJSON(xhr.responseText);

                TableBuilder.showErrorNotification(errorResult.message);
                TableBuilder.hidePreloader();
            }
        });
    }
</script>
