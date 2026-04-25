<div class="imgInfoBox-relative-block">
    <label class="input state-success"><input type="text" id="{{$fieldName }}" value="{{$entity->$fieldName}}" name="{{$fieldName}}" class="form-control datepicker">
        <span class="input-group-addon form-input-icon"><i class="fa fa-calendar"></i></span>
    </label>
</div>

<script>
    jQuery(document).ready(function() {
        jQuery("#{{$fieldName}}").datetimepicker({
            changeMonth: true,
            numberOfMonths: {{ $field['months'] ? : '1' }},
            prevText: '<i class="fa fa-chevron-left"></i>',
            nextText: '<i class="fa fa-chevron-right"></i>',
            dateFormat: "yy-mm-dd",
            timeFormat: 'HH:mm:ss',
            //showButtonPanel: true,
            regional: ["ru"],
            onClose: function (selectedDate) {}
        });
    });
</script>
