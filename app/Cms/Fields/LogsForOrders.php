<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Virtual;

class LogsForOrders extends Virtual
{
    public function getFieldForm($definition)
    {
        return view('cms.fields.logs_for_orders');
    }
}
