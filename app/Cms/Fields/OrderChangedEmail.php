<?php

namespace App\Cms\Fields;

use App\Models\OrderReceipt;
use Vis\Builder\Fields\Virtual;

class OrderChangedEmail extends Virtual
{
    public function getFieldForm($definition)
    {
        $field = $this;

        if ($this->getAllData()) {
            $id = request()->all()['id'];
 
            return view('cms.fields.email_orderchanged', compact('definition', 'id', 'field'))->render();
        }

        return '';
    }
}
