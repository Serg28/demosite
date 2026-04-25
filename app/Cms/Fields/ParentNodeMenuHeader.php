<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Hidden;

class ParentNodeMenuHeader extends Hidden
{
    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        return view('admin::form.fields.hidden', compact('definition', 'field'))->render();
    }

    public function getValueForList($definition)
    {
        return '';
    }
}
