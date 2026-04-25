<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
    <h4 class="modal-title" id="modal_form_label">
        @if($entity->id)
            {{__cms('Редактирование галереи')}} # {{$entity->id}}: {{$entity->title}} ({{$entity->created_at}})
        @else
            {{__cms('Создание галереи')}}
        @endif
    </h4>
</div>
<div class="modal-body row" data-gallery_id="{{$entity->id}}" >
    <div class="tb-uploaded-image-container col-xs-8">
            <ul class="dop_foto image-storage-sortable">
                @forelse ($entity->videos as $key => $video)
                    <li id="{{$video->id}}" class="image-storage-sortable-item {{ $video->pivot->is_preview ? "preview" : ""}}">
                        @include('image-storage::video.partials.single', ['entity' => $video])
                        <div class="tb-btn-delete-wrap">
                            <button class="btn2 btn-default btn-sm tb-btn-image-delete delete-relation-button"
                                    type="button"
                                    onclick="ImageStorage.deleteGalleryRelation({{ $video->id }},{{ $entity->id }});">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </li>
                @empty
                    <div class="image-storage-sortable-no_photo">
                        {{__cms('Нет видео')}}
                    </div>
                 @endforelse
            </ul>
    </div>

    <div class="imgInfoBox-container col-xs-4">
            <form class="smart-form" id="imgInfoBox-form-table">
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
                 <section><label>{{__cms('Теги')}}</label>
                        <select name="relations[image-storage-tags][]" multiple class="imgInfoBox-select image-storage-select">
                            @foreach ($relatedEntities['tags'] as $tag)
                                <option {{$entity->tags->contains($tag->id) ? 'selected="selected"' : ''}} value="{{$tag->id}}">{{$tag->title}}</option>
                            @endforeach
                        </select>
                    </section>
                </fieldset>

                <div class="well action-buttons-row">
                    <a onclick="ImageStorage.doSaveInfoInTable({{ $entity->id }});" href="javascript:void(0);"
                       class="btn btn-success btn-sm pull-right j-btn-save">{{__cms('Сохранить')}}</a>
                    @if($entity->id)
                    <a onclick="ImageStorage.doDeleteInTable({{ $entity->id }});" href="javascript:void(0);"
                       class="btn btn-danger btn-sm pull-left j-btn-del">{{__cms('Удалить')}}</a>
                    @endif
                </div>
            </form>
    </div>
</div>
