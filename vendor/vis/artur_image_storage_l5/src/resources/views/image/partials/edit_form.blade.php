<div class="superbox-show image-storage-popup">
<ul id="image-storage-images-sizes-tabs" class="nav nav-tabs bordered">
    @foreach ($entity->getConfigSizes() as $ident => $info)
        <li class="{{$info['default_tab'] ? 'active':""}}">
            <a style="color: #000000 !important;" href="#image-storage-images-size-{{ $ident }}" data-toggle="tab">{{ __cms($info['caption']) }}</a>
        </li>
    @endforeach
</ul>

<div id="image-storage-tabs-content" class="tab-content padding-10">
    @foreach ($entity->getConfigSizes() as $ident => $info)
        <div class="tab-pane fade {{$info['default_tab'] ? 'in active':""}}" id="image-storage-images-size-{{ $ident }}">
            <div class="image-storage-images-sizes-content">
                @include('image-storage::image.partials.tab_content')
            </div>
            <div class="image-storage-images-sizes-control_row">
                <div class="pull-left button-block">
                    <a download="{{ $entity->title ? $entity->title .'('. $info['caption'] .')' : ''}}"
                       target="_blank"
                       href="{{ asset($entity->getSource($ident)) }}"
                       class="image-storage-btn-download btn btn-default btn-sm">
                        {{ __cms("Скачать")}}
                    </a>
                    <a class="image-storage-btn-clipboard-copy btn btn-default btn-sm">{{ __cms("Копировать ссылку")}}</a>
                </div>

{{--                <div class="pull-left button-block">
                    <a  class="btn btn-default btn-sm">
                        {{ __cms("Изменить")}}
                    </a>
                </div>
--}}

                <div class="pull-right">
                    <form class="smart-form">
                    <div class="input input-file image-storage-images">
                        <span class="button">
                            <input type="file" name="image" accept="image/*" onchange="ImageStorage.replaceSingleFile(this, '{{ $ident }}', '{{$entity->id}}');">
                            {{ __cms("Выбрать")}}
                        </span>
                        <input type="text" readonly="readonly" placeholder="{{__cms("Заменить изображение")}}">
                    </div>
                    </form>
                </div>

            </div>
        </div>
    @endforeach
</div>

    <div id="imgInfoBox" class="superbox-imageinfo inline-block">
        <div></div>
        <div class="imgInfoBox-container">
            <div class="imgInfoBox-title"># {{ $entity->id }}: {{ $entity->title }} ({{ $entity->created_at }})</div>
            <form class="smart-form" id="imgInfoBox-form">
            <fieldset>
                <div class="imgInfoBox-container-content tab-content padding-10">
                    @foreach ($fields as $fieldName => $field)
                        <section>
                            <div class="tab-pane active">
                                @if(isset($field['tabs']))
                                    @include('image-storage::partials.form-field-tabbed')
                                @else
                                    @include('image-storage::partials.form-field')
                                @endif
                            </div>
                        </section>
                    @endforeach
                </div>
            </fieldset>
            <fieldset>
                <section><label>{{__cms('Галереи')}}</label>
                    <select name="relations[image-storage-galleries][]" multiple class="imgInfoBox-select image-storage-select">
                        @foreach ($relatedEntities['galleries'] as $gallery)
                            <option {{$entity->galleries->contains($gallery->id) ? 'selected="selected"' : ''}} value="{{$gallery->id}}">{{$gallery->title}}</option>
                        @endforeach
                 </select>
                </section>
                <section><label>{{__cms('Теги')}}</label>
                    <select name="relations[image-storage-tags][]" multiple class="imgInfoBox-select image-storage-select">
                        @foreach ($relatedEntities['tags'] as $tag)
                            <option {{$entity->tags->contains($tag->id) ? 'selected="selected"' : ''}} value="{{$tag->id}}">{{$tag->title}}</option>
                        @endforeach
                    </select>
                </section>
            </fieldset>

            <div class="well action-buttons-row">
                <a onclick="ImageStorage.doSaveInfo({{ $entity->id }});" href="javascript:void(0);"
                   class="btn btn-success btn-sm pull-right j-btn-save">{{__cms('Сохранить')}}</a>
                @if($entity->id)
                <a onclick="ImageStorage.doDelete({{ $entity->id }});" href="javascript:void(0);"
                   class="btn btn-danger btn-sm pull-left j-btn-del">{{__cms('Удалить')}}</a>
                @endif
            </div>
        </form>
        </div>
    </div>

<div class="superbox-close txt-color-white"><i class="fa fa-times fa-lg"></i></div>
</div>
