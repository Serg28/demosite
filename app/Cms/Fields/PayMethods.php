<?php

namespace App\Cms\Fields;

use App\Models\User;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Foreign;

class PayMethods extends Foreign
{
    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        return view('cms.fields.foreignpaymethods', compact('definition', 'field'))->render();
    }

    public function getOptions($definition)
    {
        $collection = $this->getDataWithWhereAndOrder($definition);
        $data = [];

        if ($this->defaultValue) {
            $data = [
                '' => $this->defaultValue
            ];
        }

        foreach ($collection as $item) {
            $data[$item->id] = strip_tags($item->name);
        }

        return $data;
    }
}
