<?php

namespace App\Cms\Definitions;

class IntegrationsTurboSms extends Integrations
{
    public $title = 'Интеграции Turbo Sms';

    public function getFilterScope($collection)
    {
        return $collection->where('slug', 'like', '%turbo_sms%');
    }
}
