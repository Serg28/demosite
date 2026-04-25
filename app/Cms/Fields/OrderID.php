<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Id;

class OrderID extends Id
{
    public function getValueForList($definition)
    {
        return '<a style="width:70px;display: block;" href="/admin/orderedit/?o='.$this->value.'" target="_blank">'.$this->value.'</a>';
    }

    public function getFieldForm($definition)
    {
        return '';
    }
}
