<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Definition;

class OrderProducts extends Definition
{
    public function getFieldForm($definition)
    {
        $field = $this;
        $order = $field->allData;

        return view('cms.fields.ordertable_definition', compact('definition', 'field', 'order'))->render();
    }

    public function getTable($definition, $parseJsonData)
    {
        $attributes = json_encode($parseJsonData);

        $model = $definition->model();

        $list = request('id') ? $model::find(request('id'))->{$this->relation}
            : (new $model())->{$this->relation};

        $order = $model::find(request('id'));

        //иногда при ручном редактировании списка товаров их суммы и итоговые суммы всего заказа могут отличаться
        //поэтому каждый раз при выводе списка товаров пересчитываем итоговые суммы всего заказа
        //и если они отличаются, обновляем:
        if ($order) {
            $order->updateCostFields();
        }
        //

        $fieldsDefinition = $this->head($definition);

        $list->map(function ($item) use ($fieldsDefinition, $definition): void {
            $item->fields = clone $fieldsDefinition;
            $fieldsDefinition->map(function ($item2, $key) use ($item, $definition): void {
                $item->fields[$key] = clone $item2;
                $item2->setValue($item);
                $item->fields[$key]->value = $item2->getValueForList($definition);
            });
        });

        $urlAction = 'actions/'.$definition->getNameDefinition();
        $isSortable = $this->getDefinitionRelation($definition)->getIsSortable();

        return [
            'html' => view(
                //'cms.fields.input_definition_table_data',
                'cms.fields.orderproductdefinition_table_data',
                compact('fieldsDefinition', 'list', 'attributes', 'urlAction', 'isSortable', 'order')
            )->render(),
            'count_records' => 0,
        ];
    }
}
