<?php

namespace App\Cms\Fields;

use Illuminate\Support\Facades\DB;
use Vis\Builder\Fields\ForeignAjax;

class ForeignDiscountCard extends ForeignAjax
{
    public function setValue($item): void
    {
        $relation = $item->{$this->options->getRelation()};
        $this->value = '';

        if ($relation) {
            $this->value = '<a href="'.$relation->getUrlCms().'" target="_blank">'.$relation->getFullName().'</a>';
        }
    }

    public function getValueForInput($definition)
    {
        $value = request()->id;
        $model = $definition->model();

        if ($value) {
            $item = $model::find($value);
            if ($item) {
                $related = $item->{$this->options->getRelation()};

                return [
                    'id' => $related->id,
                    'name' => $related->getFullName().',  '.$related->phone,
                ];
            }
        }
    }

    public function search($definition)
    {
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $where = $this->options->getWhereCollection();

        $modelRelated = $modelRelated
            ->where(function ($query): void {
                $query->where('code', 'like', request()->q.'%')
                    ->orWhere('barcode', 'like', request()->q.'%')
                    ->orWhere('phone', 'like', '%'.request()->q.'%')
                    ->orWhere('email', 'like', '%'.request()->q.'%');
            })->active();

        if (count($where)) {
            foreach ($where as $param) {
                $modelRelated = $modelRelated->where($param['field'], $param['eq'], $param['value']);
            }
        }

        $result = $modelRelated->select(DB::raw('id, CONCAT(`code`, " ", `barcode` ) as name'))->take(10)->get()->toArray();

        return [
            'results' => $result,
        ];
    }

    public function getFieldForm($definition)
    {
        $field = $this;

        return view('admin::form.fields.foreignajax', compact('definition', 'field'))->render();
    }

    public function getFilterInput($list)
    {
        $field = $this;
        $filterValue = $this->getFilter($list);
        $definition = $list->getDefinition();

        return view('admin::list.filters.foreignajax', compact('field', 'filterValue', 'definition'));
    }
}
