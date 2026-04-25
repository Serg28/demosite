<?php

namespace App\Cms\Definitions;

class IntegrationsLiqpay extends Integrations
{
    public $title = 'Интеграции LiqPay';

    public function getFilterScope($collection)
    {
        return $collection->where('slug', 'like', '%liqpay%');
    }
}
