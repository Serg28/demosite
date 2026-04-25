<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
    <h4 class="modal-title" id="modal_form_label">
        @if($entity->id)
            {{__cms('Редактирование тега')}} # {{$entity->id}}: {{$entity->title}} ({{$entity->created_at}})
        @else
            {{__cms('Создание тега')}}
        @endif
    </h4>
</div>
<div class="modal-body row">
    <div class="imgInfoBox-container col-xs-12">
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
