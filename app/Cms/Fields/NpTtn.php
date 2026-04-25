<?php

namespace App\Cms\Fields;

use App\Models\Order;
use Vis\Builder\Fields\Virtual;

class NpTtn extends Virtual
{
    public function getFieldForm($definition)
    {
        if ($this->getAllData()) {
            $id = request()->all()['id'];
            $order = Order::where('id', $id)->first();

            return view('cms.fields.np_ttn', compact('definition', 'id', 'order'))->render();
        }

        return '';
    }
}
