<?php

namespace App\Cms\Definitions;

class SettingsCode extends SettingsCurrency
{
    public $title = 'Установка кода';

    public function getFilterScope($collection)
    {
        return $collection->filter('code');
    }
}
