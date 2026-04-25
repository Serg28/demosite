<ul class="nav nav-tabs tabs-pull-right">
    <label class="label pull-left">{{__cms($field['caption'])}}</label>
    @foreach($field['tabs'] as $lang => $tab)
        <li class="{{$loop->first ? 'active' : ''}}"> <a style="color: #000000 !important;" href="#e-{{$fieldName}}-{{$tab['postfix']}}-{{$loop->index}}"
               data-toggle="tab">{{$tab['caption']}}</a>
        </li>
    @endforeach
</ul>

<div class="tab-content padding-5">
    @foreach($field['tabs'] as $lang => $tab)
        <div class="tab-pane {{$loop->first ? 'active' : ''}}" id="e-{{$fieldName}}-{{$tab['postfix']}}-{{$loop->index}}">
            @include('image-storage::partials.fields_types.field_' . $field['type'], ['fieldName' =>  $fieldName . $tab['postfix']])
        </div>
    @endforeach
</div>
