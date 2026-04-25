<?php

namespace App\Cms\Definitions;

class SettingsCurrency extends SettingsForUser
{
    public $title = 'Валюта';

    public function getFilterScope($collection)
    {
        return $collection->filter('currency');
    }
}
