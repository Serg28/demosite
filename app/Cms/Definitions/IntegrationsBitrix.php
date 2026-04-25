<?php

namespace App\Cms\Definitions;

class IntegrationsBitrix extends Integrations
{
    public $title = 'Интеграции Bitrix';

    public function getFilterScope($collection)
    {
        return $collection->where('slug', 'like', '%bitrix%');
    }
}
