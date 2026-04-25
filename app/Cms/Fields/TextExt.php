<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Text;

class TextExt extends Text
{
    private bool $autoTranslate;

    public function autoTranslate(bool $flag = false)
    {
        $this->isAutoTranslate = $flag;
        $this->autoTranslate = $flag;

        return $this;
    }

    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        return view('cms.fields.' . $nameField, compact('definition', 'field'))->render();
    }
}
