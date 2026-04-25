<?php

namespace App\Cms\Definitions;

class IntegrationsNovaposhta extends Integrations
{
    public $title = 'Интеграции Новая Почта';

    public function getFilterScope($collection)
    {
        return $collection->where('slug', 'like', '%novaposhta%')->orWhere('slug', 'like', '%np%');
    }
}
