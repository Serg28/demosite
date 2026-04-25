<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Virtual;

class OrderProductNum extends Virtual
{
    protected $options = [];

    public $i = 1;

    public function options($model)
    {
        $this->options = $model;

        return $this;
    }

    public function getFieldForm($definition)
    {
        return '';
    }

    public function getValueForList($definition): string
    {
        //return array_keys($this->getAllData());
        return $this->i++;
    }
}
