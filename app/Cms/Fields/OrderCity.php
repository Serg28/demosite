<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\ForeignAjax;

class OrderCity extends ForeignAjax
{
    private bool $isWarehouseResetFieldsOnChange = false;

    public function setValue($item)
    {
        $relation = $item->{$this->options->getRelation()}()
            ->select(['id', "{$this->options->getKeyField()} as name"])
            ->first();

        $this->value = '';

        if ($relation) {
            $this->value = $relation->name;
        }
    }

    public function getValueForList($definition)
    {
        return $this->getValue();
    }

    public function getValueForExel($definition)
    {
        return $this->getValueForList($definition);
    }

    public function getValueForInput($definition)
    {
        $value = request()->id;
        $model = $definition->model();

        if ($value) {
            $item = $model::find($value);
            if ($item) {
                $related = $item->{$this->options->getRelation()}()
                    ->select(['id', "{$this->options->getKeyField()} as name"])
                    ->first();

                return [
                    'id' => $related->id,
                    'name' => $related->name,
                ];
            }
        }
    }

    public function getValueForFilter($definition, $id)
    {
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $selectOption = $modelRelated::find($id);

        return $selectOption->{$this->options->getKeyField()};
    }

    /*
    //Базовый вариант - города без областей в названии
    public function search($definition)
    {
        $keyField = $this->options->getKeyField();
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $where = $this->options->getWhereCollection();

        $modelRelated = $modelRelated->where($keyField, 'like', '%'.$this->convertQuery(request()->q).'%');

        if (count($where)) {
            foreach ($where as $param) {
                $modelRelated = $modelRelated->where($param['field'], $param['eq'], $param['value']);
            }
        }

        $result = $modelRelated->take(10)->get(['id', $keyField.' as name'])->toArray();

        return [
            'results' => $result,
        ];
    }
    */

    //У города выводится область
    public function search($definition)
    {
        $keyField = $this->options->getKeyField();
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $where = $this->options->getWhereCollection();

        $modelRelated = $modelRelated->where($keyField, 'like', $this->convertQuery(request()->q).'%');

        if (count($where)) {
            foreach ($where as $param) {
                $modelRelated = $modelRelated->where($param['field'], $param['eq'], $param['value']);
            }
        }

        $result = $modelRelated->with('regions')
        ->take(10)
            ->get(['id', $keyField.' as name', 'region_id'])
            ->map(function ($item) {
                $region = $item->regions? ' ('.$item->regions->t('title').' ' . __cms('область') . ')' : '';
                // Добавляем регион в поле name
                $item['name'] .= $region;
                return $item;
            })
            ->toArray();

        return [
            'results' => $result,
        ];
    }


    public function resetWarehouseFieldsOnChange(bool $flag = true)
    {
        $this->isWarehouseResetFieldsOnChange = $flag;

        return $this;
    }

    public function isWarehouseResetFieldsOnChange(): bool
    {
        return $this->isWarehouseResetFieldsOnChange;
    }

    public function getFieldForm($definition)
    {
        $field = $this;

        return view('cms.fields.ordercity', compact('definition', 'field'))->render();
    }

    public function getFilterInput($list)
    {
        $field = $this;
        $filterValue = $this->getFilter($list);
        $definition = $list->getDefinition();

        return view('admin::list.filters.foreignajax', compact('field', 'filterValue', 'definition'));
    }
}
