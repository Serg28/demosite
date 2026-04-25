<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Virtual;

class Account extends Virtual
{
    public function getFieldForm($definition)
    {
        $field = $this;

        return $field->getAllData() ?
            view('cms.fields.account', compact('definition', 'field'))->render() :
            '';
    }
}
