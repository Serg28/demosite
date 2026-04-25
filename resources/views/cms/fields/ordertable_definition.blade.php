<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div>@if(@$order->order_status_id!==7) {{-- Запрещаем для завершеного заказа --}}
                <button class="btn btn-sm btn-success" type="button"
                        onclick="ForeignDefinition.createDefinition($(this), '{{$field->getDefinitionRelation($definition)->model()->getTable()}}', '{{$field->getAttributes($definition)}}', {{request('id')}})">{{__cms('Добавить')}}</button>
                @endif
                <div class="loader_create_definition hide loader_definition_{{$field->getNameField()}}"></div>

                <div class="definition_blocks definition_{{$field->getNameField()}}">
                    <p style="text-align: center">{{__cms('Загрузка..')}}</p>
                </div>
                <script>
                    ForeignDefinition.callbackForeignDefinition('{{request('id')}}', '{!! $field->getAttributes($definition) !!}', 'actions');
                </script>
            </div>

        </div>
    </div>
</section>
