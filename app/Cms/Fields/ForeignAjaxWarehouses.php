<?php

namespace App\Cms\Fields;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Vis\Builder\Fields\ForeignAjax;

class ForeignAjaxWarehouses extends ForeignAjax
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
        /*
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

        */



        $result = $modelRelated
            ->where('city_id', $cityId)
            ->where(function ($query) use ($request, $modelRelated) {
                $query->when(Schema::hasColumn(
                    $modelRelated->getTable(),
                    'postcode'
                ) && filled($request), function ($query) use ($request) {
                    $query->where('postcode', 'Like', '%' . $request . '%');
                    if ($query->count() == 0) {
                        $query->orWhere('title->' . App::getLocale(), 'like', '%' . $request . '%');
                    }
                })
                    ->when(
                        Schema::hasColumn($modelRelated->getTable(), 'num') && filled($request),
                        function ($query) use ($request) {
                            $query->where('num', 'Like', $request . '%');
                            if ($query->count() == 0) {
                                $query->orWhere('title->' . App::getLocale(), 'like', '%' . $request . '%');
                            }
                        }
                    );
            })->with('city') // Загрузка связанных данных по городу
            ->take(10)
            ->get(['id', $keyField.' as name'])->toArray();


        return [
            'results' => $result,
        ];
    }






    public function getFieldForm($definition)
    {
        $field = $this;

        return view('cms.fields.foreignajaxext_warehouses', compact('definition', 'field'))->render();
    }

    public function getFilterInput($list)
    {
        $field = $this;
        $filterValue = $this->getFilter($list);
        $definition = $list->getDefinition();

        return view('admin::list.filters.foreignajax', compact('field', 'filterValue', 'definition'));
    }
}
