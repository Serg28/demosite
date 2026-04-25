<?php

namespace App\Cms\Fields;

use Illuminate\Support\Str;
use Vis\Builder\Fields\Textarea;

class OrderAdminComment extends Textarea
{
    public function getValueForList($definition)
    {
        return Str::limit(strip_tags($this->value), 150);
    }

    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        return view('admin::form.fields.textarea', compact('definition', 'field'))->render();
    }
}
