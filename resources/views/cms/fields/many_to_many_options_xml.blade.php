<?php
$selected = $field->getOptionsSelected($definition);

?>

<style>
    .ui-multiselect .ui-widget-header,.ui-multiselect  .ui-widget-header  a {
        color: #fff;
    }
</style>

<section class="{{$field->getClassName()}}" id="options_for_xml">
    <select class="multiselect" multiple="multiple" name="{{ $field->getNameField()}}[]" id="{{ $field->getNameField()}}">
        @if (isset($selected) && count($selected))
            @foreach($selected as $id => $selectOption)
                <option value="{{$id}}" selected>{{$selectOption}}</option>
            @endforeach
        @endif
        @foreach ($field->getOptions($definition) as $key => $title)
            @if (!isset($selected[$key]))
                <option value="{{$key}}">{{ trim($title) }}</option>
            @endif
        @endforeach
    </select>
</section>

<script type="text/javascript">
    $('[name=characteristic_id]').change(function () {

        $.post( "/admin/xml_feeds/load_options",
            {
                'characteristic_id' : $(this).val(),
                'field' : '{{ $field->getNameField()}}'
            },
            function( data ) {
                $("#options_for_xml").html(data);
            });

    });
    $(document).ready(function () {
        $(".multiselect").multiselect();
    });
</script>
