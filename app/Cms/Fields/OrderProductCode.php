<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Virtual;

class OrderProductCode extends Virtual
{
    protected $options = [];

    public function options($model)
    {
        $this->options = $model;

        return $this;
    }

    public function getFieldForm($definition)
    {
        return '';
    }

    public function setValue($item): void
    {
        $relation = $item->{$this->options->getRelation()};
        $this->value = '';

        if ($relation) {
            $this->value = $relation->code;
        }
    }
}
