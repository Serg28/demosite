<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Virtual;

class OrderNum extends Virtual
{

    protected $options = [];

    public function options($model)
    {
        $this->options = $model;

        return $this;
    }

    public function getValueForList($definition)
    {
        $item = $this->allData;
        $num = $item->num ?: $item->id;
        return '<a style="width:70px;display: block;" href="/admin/orderedit/?o='.$item->id.'" target="_blank">'.$num.'</a>';
    }

    public function getFieldForm($definition)
    {
        return '';
    }
}
