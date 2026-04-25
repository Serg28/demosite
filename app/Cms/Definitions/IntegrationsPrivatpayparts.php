<?php

namespace App\Cms\Definitions;

class IntegrationsPrivatpayparts extends Integrations
{
    public $title = 'Интеграции Приват оплата частями';

    public function getFilterScope($collection)
    {
        return $collection->where('slug', 'like', '%privat%')->whereNot('slug', 'like', '%liqpay%');
    }
}
