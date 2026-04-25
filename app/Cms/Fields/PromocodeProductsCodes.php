<?php

namespace App\Cms\Fields;

use Illuminate\Support\Facades\Cache;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\ManyToManyAjax;

class PromocodeProductsCodes extends ManyToManyAjax
{
    protected $transliterationField;

    protected $transliterationOnlyEmpty;

    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();
        $data = $field->allData;

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        return view('cms.fields.many_to_many_productscodes', compact('definition', 'field', 'data'))->render();
    }

    public function getTraslationField(): ?string
    {
        return $this->transliterationField;
    }

    public function getTraslationOnlyEmpty(): ?bool
    {
        return $this->transliterationOnlyEmpty;
    }

    public function getOptionsSelected(Resource $definition)
    {
        if (request()->id) {
            //Вариант с таблицей products - проверка существования товаров и вывод в поле только тех, которые существуют
            /*$selected = $definition->model()->find(request()->id)->saleProducts()->withpivot('product_code')
                ->select(["products.code", $this->options->getKeyField()." as name"])
                ->get();
            foreach ($selected as $item) {
                $result[$item->code] = $item->name;
            }
            return $result;
            */

            //Вариант вывода кодом только из pivot - проверка существования товаров нету
            $selected = $definition->model()->find(request()->id)->product_promocode()
                ->select(['product_code'])
                ->get();
            $result = [];

            foreach ($selected as $item) {
                $result[$item->product_code] = $item->product_code;
            }

            return $result;
        }

        return [];
    }

    public function getArticulsList(Resource $definition)
    {
        $result = $this->getOptionsSelected($definition);

        return implode(',', array_keys($result));
    }

    public function save($collectionString, $model)
    {
        $collectionArray = explode(',', $collectionString);

        $model->{$this->options->getRelation()}()->detach();

        if ($collectionString) {
            $model->{$this->options->getRelation()}()->syncWithoutDetaching($collectionArray);
        }

        Cache::tags(['promocode_product_'.$model->id, 'promocode_'.$model->id])->flush();
    }
}
