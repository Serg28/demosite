<?php

namespace App\Cms\Definitions;

class Integrations extends SettingsForUser
{
    public $title = 'Интеграции';

    public function getFilterScope($collection)
    {
        return $collection->filter('integration');
    }
}
