<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Virtual;

class Order1cProcessed extends Virtual
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

    public function getValueForList($definition): string
    {
        if ($this->fastEdit) {
            $idRecord = $this->getId();
            $isChecked = $this->getValue();
            $field = $this->getNameFieldInBd();

            return view('admin::list.fast_edit.checkbox', compact('idRecord', 'isChecked', 'field'));
        }

        if ($this->value == 1) {
            return '<span class="glyphicon glyphicon-ok"></span>';
        }

        return '<span class="glyphicon glyphicon-minus"></span>';
    }
}
