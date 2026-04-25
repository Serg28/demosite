<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\ForeignAjax;

class ForeignAjaxCity extends ForeignAjax
{
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
        return $modelRelated::find($id)->{$this->options->getKeyField()};
    }

    //У города выводится область
    public function search($definition): array|null
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


    public function getFieldForm($definition): string
    {
        $field = $this;

        return view('cms.fields.foreignajaxext', compact('definition', 'field'))->render();
    }

    public function getFilterInput($list)
    {
        $field = $this;
        $filterValue = $this->getFilter($list);
        $definition = $list->getDefinition();

        return view('admin::list.filters.foreignajax', compact('field', 'filterValue', 'definition'));
    }
}
