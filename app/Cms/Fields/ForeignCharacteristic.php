<?php

namespace App\Cms\Fields;

use App\Models\Product;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Foreign;

class ForeignCharacteristic extends Foreign
{
    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        return view('cms.fields.'.$nameField, compact('definition', 'field'))->render();
    }

    public function getDataWithWhereAndOrder(Resource $definition)
    {
        $idProduct = request('foreign_field_id') ?? request('id');

        if (request('product_id')) {
            $idProduct = request('product_id');
        }

        /*
        //$product = Product::find($idProduct);
        $product = $modelRelated->with(['category.characteristics:id,'.$this->options->getKeyField().' as name','category'])->select(['id','category_id'])->find($idProduct);

        $characteristics = $product && $product->category ? $product->category->characteristics() : null;

        return $characteristics ? $characteristics->select(['characteristics.id', 'characteristics.'.$this->options->getKeyField().' as name'])->get(['id','name']) : collect();
        */

        $definition = $this->getDefinition($definition);
        $characteristics = $definition->model()->{$this->options->getRelation()}()->getRelated();
        return $characteristics ? $characteristics->select(['characteristics.id', 'characteristics.'.$this->options->getKeyField().' as name'])->get(['id','name']) : collect();
    }
}
