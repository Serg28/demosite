<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Virtual;

class Printing extends Virtual
{
    public function getFieldForm($definition)
    {
        $field = $this;

        return $field->getAllData() ?
            view('cms.fields.printing', compact('definition', 'field'))->render() :
            '';
    }
}
