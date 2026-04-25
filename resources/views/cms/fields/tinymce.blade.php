<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <div class="no_active_froala">
                 <textarea class="text_block_ tinymce" name="{{ $field->getNameField()}}"
                            toolbar = "{{$field->getToolbar()}}"
                            inlineStyles = ''
                            options = '{{ $field->getOptions()}}'>{{ $field->getValue()  }}</textarea>
                </div>
                @if ($field->getComment())
                    <div class="note">
                        {!! $field->getComment() !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
<script src="{{ asset('packages/tinymce4/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script>
    //https://pluginza.com/plugins/responsive-file-manager
    tinymce.init({
        selector: 'textarea.tinymce', // Replace this CSS selector to match the placeholder element for TinyMCE
        language: '{{$thisLang }}',
        skin: 'lightgray', //custom
        plugins: '{{$field->getPlugins()}}',
        toolbar: '{{$field->getToolbar()}}',
        external_filemanager_path: '/packages/tinymce4/plugins/responsivefilemanager/filemanager/',
        templates: {!! $field->getTemplates() !!},
        setup: function (editor) {
            editor.on('change', function () {
                tinymce.triggerSave();
            });
        }
    });
</script>
