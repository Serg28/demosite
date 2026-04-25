<?php

namespace App\Cms\Definitions;

class IntegrationsFacebook extends Integrations
{
    public $title = 'Интеграции Facebook';

    public function getFilterScope($collection)
    {
        return $collection->where('slug', 'like', '%facebook%');
    }
}
