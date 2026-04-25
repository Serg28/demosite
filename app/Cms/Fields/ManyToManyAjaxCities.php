<?php

namespace App\Cms\Fields;

use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\ManyToManyAjax;

class ManyToManyAjaxCities extends ManyToManyAjax
{
    public function getDataWithWhereAndOrder(Resource $definition)
    {
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $collection = $modelRelated::select(['id', $this->options->getKeyField().' as name', 'origin']);
        $where = $this->options->getWhereCollection();
        $order = $this->options->getOrderCollection();

        $collection = $collection
            ->where(function ($query): void {
                $query->where('title->ru', 'like', request()->q.'%')->orWhere('title->en', 'like', request()->q.'%')->orWhere('title->ua', 'like', request()->q.'%');
            });

        if (count($where)) {
            foreach ($where as $param) {
                $collection = $collection->where($param['field'], $param['eq'], $param['value']);
            }
        }

        if (count($order)) {
            foreach ($order as $param) {
                $collection = $collection->orderBy($param['field'], $param['order']);
            }
        }

        if (request()->q) {
            $query = mb_convert_case(request('q'), MB_CASE_TITLE, 'UTF-8');

            $collection = $collection
                ->where($this->options->getKeyField(), 'like', '%'.$query.'%')
                ->orWhere('title->ru', 'like', $query.'%')->orWhere('title->en', 'like', $query.'%')->orWhere('title->ua', 'like', $query.'%');
        }

        return $collection->get()->mapWithKeys(function ($item, $key) {
            return [$key => ['id' => $item->id, 'name' => !empty($item->t('origin'))?$item->t('origin'):$item->t('name')]];
        });

    }

    public function getFieldForm($definition)
    {
        $field = $this;

        return view('admin::form.fields.manytomanyajax', compact('definition', 'field'))->render();
    }

    /**
     * @param $definition
     */
    public function getValueForList($definition): string
    {
        $products = $this->getValue();

        return  view('cms.lists.manytomany_products', compact('definition', 'products'))->render();
    }
}
