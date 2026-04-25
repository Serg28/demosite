<?php

namespace App\Cms\Definitions;

class IntegrationsMonopayparts extends Integrations
{
    public $title = 'Интеграции Монобанк оплата частями';

    public function getFilterScope($collection)
    {
        return $collection->where('slug', 'like', '%mono%');
    }
}
