<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Checkbox;

class CloseFilter extends Checkbox
{
    public function getValueForList($definition): string
    {
        if ($this->fastEdit) {
            $idRecord = $this->getId();
            $isChecked = $this->getValue();
            $field = $this->getNameFieldInBd();

            return view('cms.fields.fast_edit.close_filter_checkbox', compact('idRecord', 'isChecked', 'field'));
        }

        if ($this->value == 1) {
            return '<span class="glyphicon glyphicon-ok"></span>';
        }

        return '<span class="glyphicon glyphicon-minus"></span>';
    }

    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        return view('admin::form.fields.checkbox', compact('definition', 'field'))->render();
    }
}
