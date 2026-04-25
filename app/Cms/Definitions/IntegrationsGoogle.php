<?php

namespace App\Cms\Definitions;

class IntegrationsGoogle extends Integrations
{
    public $title = 'Интеграции Google';

    public function getFilterScope($collection)
    {
        return $collection->where('slug', 'like', '%google%');
    }
}
