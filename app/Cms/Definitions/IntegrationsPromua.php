<?php

namespace App\Cms\Definitions;

class IntegrationsPromua extends Integrations
{
    public $title = 'Интеграции Prom.ua';

    public function getFilterScope($collection)
    {
        return $collection->where('slug', 'like', '%promua%');
    }
}
