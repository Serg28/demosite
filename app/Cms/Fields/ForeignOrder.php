<?php

namespace App\Cms\Fields;

use App\Models\Order;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Foreign;

class ForeignOrder extends Foreign
{
    /*public function setValue($item): void
    {
        $this->allData = $item;
        $relation = $item->{$this->options->getRelation()};
        $this->value = '';

        if ($relation) {
            $this->value = '<a href="' . $relation->getUrl() . '" target="_blank">' . $relation->id . '</a>';
            return;
        }

        //$this->value = $item->order_id;
    }*/

    public function getValueForList($definition)
    {
        $data = $this->getAllData();

        if ($data->order_id) {
            return '<a target="_blank" href="/admin/orders?id='.$data->order_id.'">'.$this->getValue().'</a>';
        }

        return $this->getValue();
    }

    public function getDataWithWhereAndOrder(Resource $definition)
    {
        return Order::selectRaw('id, CONCAT(id, " - (", first_name, " ", last_name, " ", phone, ")" ) as name')->orderBy('id', 'desc')->get();
    }

    public function getValueForExel($definition)
    {
        return $this->getValue();
    }

    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();
        $orderId = $this->allData && $this->allData->order_id
            ? $this->allData->order_id
            : '';

        return view('cms.fields.'.$nameField, compact('definition', 'field', 'orderId'))->render();
    }
}
