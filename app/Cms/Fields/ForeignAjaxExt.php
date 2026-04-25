<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\ForeignAjax;

class ForeignAjaxExt extends ForeignAjax
{
    private array $searchParams = [];

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

        $result = $modelRelated->take($this->searchParams['limit'] ?? 20)->get(['id', $keyField.' as name'])->toArray();

        return [
            'results' => $result,
        ];
    }

    public function setSearchLimit(int $limit = 20)
    {
        $this->searchParams['limit'] = $limit;

        return $this;
    }

    public function setMinimumSearchLength(int $length = 1)
    {
        $this->searchParams['minimum_length'] = $length;

        return $this;
    }

    public function getFieldForm($definition)
    {
        $field = $this;

        $search = $this->searchParams;

        return view('cms.fields.foreignajaxext', compact('definition', 'field', 'search'))->render();
    }

    public function getFilterInput($list)
    {
        $field = $this;
        $filterValue = $this->getFilter($list);
        $definition = $list->getDefinition();

        return view('admin::list.filters.foreignajax', compact('field', 'filterValue', 'definition'));
    }
}
