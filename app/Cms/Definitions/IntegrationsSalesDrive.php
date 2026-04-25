<?php

namespace App\Cms\Definitions;

class IntegrationsSalesDrive extends Integrations
{
    public $title = 'Интеграции Sales Drive';

    public function getFilterScope($collection)
    {
        return $collection->where('slug', 'like', '%sales-drive%');
    }
}
