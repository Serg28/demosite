<?php

namespace App\Cms\Fields;

use App\Models\Product;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Foreign;

class ForeignCharacteristicOptions extends Foreign
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
        $product = Product::find(request('foreign_field_id'));

        return $product->category->characteristics()->select(['characteristics.id', $this->options->getKeyField().' as name'])->get();
    }
}
