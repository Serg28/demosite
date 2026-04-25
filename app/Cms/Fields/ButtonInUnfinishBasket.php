<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Virtual;

class ButtonInUnfinishBasket extends Virtual
{
    public function getFieldForm($definition)
    {
        $field = $this;

        return view('cms.fields.button_in_unfinish_basket', compact('definition', 'field'))->render();
    }
}
