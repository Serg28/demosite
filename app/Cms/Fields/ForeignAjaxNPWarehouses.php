<?php

namespace App\Cms\Fields;

use Illuminate\Support\Facades\App;
use Vis\Builder\Fields\ForeignAjax;

class ForeignAjaxNPWarehouses extends ForeignAjax
{
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

    /*public function search($definition)
    {
        $keyField = $this->options->getKeyField();
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $where = $this->options->getWhereCollection();

        //$modelRelated = $modelRelated->where($keyField, 'like', '%'.$this->convertQuery(request()->q).'%');

        //--
        $request = $this->convertQuery(request()->q);

        $modelRelated = $modelRelated->where('num', 'Like', $request . '%');
        if ($modelRelated->count() == 0) {
            $modelRelated->orWhere('title->' . App::getLocale(), 'like', '%' . $request . '%');
        }
        //--

        if (count($where)) {
            foreach ($where as $param) {
                $modelRelated = $modelRelated->where($param['field'], $param['eq'], $param['value']);
            }
        }

        //$result = $modelRelated->take(10)->get(['id', $keyField.' as name'])->toArray();
        //--
        $result = $modelRelated->take(10)->orderByRaw('ISNULL(num), num ASC')->orderBy(
            'title->' . App::getLocale(),
            'asc'
        )->get(['id', $keyField.' as name'])->toArray();
        //--

        return [
            'results' => $result,
        ];
    }*/

    public function search($definition)
    {
        $keyField = $this->options->getKeyField();
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $where = $this->options->getWhereCollection();

        $request = $this->convertQuery(request()->q);
        $orderId = request()->id ?? null;

        //$cityId = $orderId ? $definition->model()::find($orderId)->city_id : null;
        $cityId = request()->cityId ?? null;

        $result = $modelRelated
            ->where('num', 'like', $request . '%')
            ->where('city_id', $cityId)
            ->orWhereHas('city', function ($query) use ($request) {
                $query->where('title->' . App::getLocale(), 'like', '%' . $request . '%');
            })
            ->when(count($where), function ($query) use ($where) {
                foreach ($where as $param) {
                    $query->where($param['field'], $param['eq'], $param['value']);
                }
            })
            ->with('city') // Загрузка связанных данных по городу
            ->take(10)
        ->get(['id', $keyField.' as name'])->toArray();

        return [
            'results' => $result,
        ];
    }




    public function getFieldForm($definition)
    {
        $field = $this;

        return view('cms.fields.foreignajaxext_np_warehouses', compact('definition', 'field'))->render();
    }

    public function getFilterInput($list)
    {
        $field = $this;
        $filterValue = $this->getFilter($list);
        $definition = $list->getDefinition();

        return view('admin::list.filters.foreignajax', compact('field', 'filterValue', 'definition'));
    }
}
