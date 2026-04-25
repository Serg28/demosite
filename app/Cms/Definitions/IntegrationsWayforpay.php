<?php

namespace App\Cms\Definitions;

class IntegrationsWayforpay extends Integrations
{
    public $title = 'Интеграции WayForPay';

    public function getFilterScope($collection)
    {
        return $collection->where('slug', 'like', '%wayforpay%');
    }
}
