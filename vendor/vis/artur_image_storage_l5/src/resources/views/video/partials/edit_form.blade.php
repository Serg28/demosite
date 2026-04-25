<div class="superbox-show image-storage-popup">
<ul id="image-storage-images-sizes-tabs" class="nav nav-tabs bordered">
        <li class="active">
            <a style="color: #000000 !important;" href="#image-storage-video" data-toggle="tab">{{__cms('Видео')}}</a>
        </li>
        @if($entity->id)
        <li class="">
            <a style="color: #000000 !important;" href="#image-storage-video-preview" data-toggle="tab">{{__cms('Превью')}}</a>
        </li>
        @endif
</ul>

<div id="image-storage-tabs-content" class="tab-content padding-10">
        <div class="tab-pane fade in active image-storage-video-iframe-wrapper" id="image-storage-video">
            @if($entity->id)
                <iframe class="image-storage-video-iframe superbox-current-img" src="{{$entity->api()->getEmbedUrl()}}" frameborder="0" allowfullscreen></iframe>
            @endif
        </div>
        @if($entity->id)
        <div class="tab-pane fade" id="image-storage-video-preview">
            <div>
                <img src="{{ asset($entity->getPreviewImage())}}" class="superbox-current-img">
            </div>
            <div class="image-storage-images-sizes-control_row">
                <div class="pull-left button-block">
                    <a download="{{ $entity->title ? $entity->title .' '. __cms('Превью') : ''}}"
                       target="_blank"
                       href="{{ asset($entity->getPreviewImage())}}"
                       class="image-storage-btn-download btn btn-default btn-sm">
                        {{ __cms("Скачать")}}
                    </a>
                    <a class="image-storage-btn-clipboard-copy btn btn-default btn-sm">{{ __cms("Копировать ссылку")}}</a>
                    <a onclick="ImageStorage.removeUploadedPreview(this,'{{$entity->id}}');"
                       href="javascript:;"
                       class="btn btn-default btn-sm">
                        {{ __cms("Удалить загруженное превью")}}
                    </a>
                </div>
                <div class="pull-right">
                    <form class="smart-form">
                        <div class="input input-file image-storage-images">
                    <span class="button">
                        <input type="file" name="image" accept="image/*" onchange="ImageStorage.uploadVideoPreview(this,'{{$entity->id}}');">
                        {{ __cms("Выбрать")}}
                    </span>
                            <input type="text" readonly="readonly" placeholder="{{__cms("Заменить изображение")}}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
</div>

    <div id="imgInfoBox" class="superbox-imageinfo inline-block">
        <div></div>
        <div class="imgInfoBox-container">
            <div class="imgInfoBox-title">
                @if($entity->id)
                    {{__cms('Редактирование видео')}} # {{ $entity->id }}: {{ $entity->title }} ({{ $entity->created_at }})
                @else
                    {{__cms('Создание видео')}}
                @endif
            </div>
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
                <section><label>{{__cms('Видеогалереи')}}</label>
                    <select name="relations[image-storage-videoGalleries][]" multiple class="imgInfoBox-select image-storage-select">
                        @foreach ($relatedEntities['videoGalleries'] as $gallery)
                            <option {{$entity->videoGalleries->contains($gallery->id) ? 'selected="selected"' : ''}} value="{{$gallery->id}}">{{$gallery->title}}</option>
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
