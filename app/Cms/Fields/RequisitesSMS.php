<?php

namespace App\Cms\Fields;

use App\Models\OrderReceipt;
use Vis\Builder\Fields\Virtual;

class RequisitesSMS extends Virtual
{
    public function getFieldForm($definition)
    {
        $field = $this;

        if ($this->getAllData()) {
            $id = request()->all()['id'];
            $receipt = OrderReceipt::where('order_id', $id)->first();

            return view('cms.fields.sms_requisites', compact('definition', 'id', 'receipt', 'field'))->render();
        }

        return '';
    }
}
