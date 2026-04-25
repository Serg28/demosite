<section class="multilang {{$field->getClassName()}}">
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
                    <div style="position: relative;" >
                        <div class="no_active_froala_" style="padding: 0">
                             <textarea class="text_block_ tinymce" name="{{ $field->getNameField()}}[{{$tab->language}}]"
                                   options = '{{ $field->getOptions()}}'>{{$field->getValueLanguage($tab->language)}}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
        @if ($field->getComment())
            <div class="note">
                {!! $field->getComment() !!}
            </div>
        @endif
    </div>
</section>


<script src="{{ asset('packages/tinymce4/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script>

    //https://pluginza.com/plugins/responsive-file-manager
    //https://github.com/trippo/ResponsiveFilemanager
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
