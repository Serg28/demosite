<div id="{{$fieldName}}-wysiwyg"
     toolbar = "{{ isset($field['toolbar']) ? $field['toolbar'] : "fullscreen, bold, italic, underline, strikeThrough, subscript, superscript, fontFamily, fontSize,  color,
  emoticons, inlineStyle, paragraphStyle,  paragraphFormat, align, formatOL, formatUL, outdent, indent, quote, insertHR,
  insertLink, insertTable, undo, redo, clearFormatting, selectAll, html"}}"

     inlineStyles = '{{ isset($field['inlineStyles']) ? json_encode($field['inlineStyles']) : ""}}'

     options = '{{ isset($field['options']) ? json_encode($field['options']) : ""}}'

     class="text_block no_active_froala" name="{{ $fieldName }}">{!!  $entity->$fieldName  !!}</div>

<textarea style="display: none" name="{{ $fieldName }}">{{$entity->$fieldName}}</textarea>

@if (isset($field['comment']) && $field['comment'])
    <div class="note">
        {{$field['comment']}}
    </div>
@endif

<script>
    jQuery(document).ready(function() {
        TableBuilder.initFroalaEditor();
    });
</script>
