<?php

namespace App\Cms\Fields;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Vis\Builder\Definitions\Resource;

class ForeignAjaxManager extends ForeignAjaxUser
{
    public function setValue($item): void
    {
        $this->allData = $item;
        $relation = $item->{$this->options->getRelation()};
        $this->value = '';

        if ($relation && !is_string($relation)) {
            $this->value = '<a href="'.$relation->getUrlCms().'" target="_blank">'.$relation->getFullName().'</a>';
            return;
        }

        if (is_string($relation)) {
            $this->value = $relation;
            return;
        }

        $this->value = '<a href="'.route('set_manager', $item).'" target="_blank" style="color:green; text-decoration: underline">'.__cms('Взять заказ').'</a>';
    }

    public function _getDataWithWhereAndOrder(Resource $definition)
    {
        return User::selectRaw('id, CONCAT(first_name, " ", last_name) as name')->with('roles')->whereHas('roles', function ($query): void {
            $query->whereNotNull('role_id');
        })->get();
    }


    public function getDataWithWhereAndOrder(Resource $definition)
    {
        return User::selectRaw('id, CONCAT(first_name, " ", last_name) as name')->hasOrderAccess()
            ->whereHas('roles', function ($query) {
                $query->whereNotNull('role_id')/*->whereIn('role_id', [1, 2, 3])*/;
            })
            ->with('roles:id,name') // Загружаем только нужные поля из roles
            ->get();
    }

    public function getFieldForm($definition)
    {
        $field = $this;
        $managerId = $this->allData && $this->allData->manager_id
            ? $this->allData->manager_id
            : '';

        return view('cms.fields.foreign_manager', compact('definition', 'field', 'managerId'))->render();
    }


    public function search($definition)
    {
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $where = $this->options->getWhereCollection();

        $modelRelated = $modelRelated->with('roles')->whereHas('roles', function ($query): void {
            $query->whereNotNull('role_id');
        })->hasOrderAccess()
            ->where(function ($query): void {
                $query->where('first_name', 'like', request()->q.'%')
                    ->orWhere('last_name', 'like', request()->q.'%')
                    ->orWhere('phone', 'like', '%'.request()->q.'%')
                    ->orWhere('email', 'like', '%'.request()->q.'%');
            });

        if (count($where)) {
            foreach ($where as $param) {
                $modelRelated = $modelRelated->where($param['field'], $param['eq'], $param['value']);
            }
        }

        $result = $modelRelated->select(DB::raw('id, CONCAT(`first_name`, " ", `last_name` ) as name'))->take(10)->get()->toArray();

        return [
            'results' => $result,
        ];
    }


}
