<?php

namespace App\Cms\Definitions;

class IntegrationsAlphaSms extends Integrations
{
    public $title = 'Интеграции AlphaSms';

    public function getFilterScope($collection)
    {
        return $collection->where('slug', 'like', '%alphasms%');
    }
}
