<?php

namespace App\Cms\Definitions;

class IntegrationsRecaptcha extends Integrations
{
    public $title = 'Интеграции Google reCAPTCHA';

    public function getFilterScope($collection)
    {
        return $collection->where('slug', 'like', '%google-recaptcha%');
    }
}
