<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Virtual;

class Invoice extends Virtual
{
    public function getFieldForm($definition)
    {
        $field = $this;

        return $field->getAllData() ?
            view('cms.fields.invoice', compact('definition', 'field'))->render() :
            '';
    }
}
