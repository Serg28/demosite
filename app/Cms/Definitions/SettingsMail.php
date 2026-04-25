<?php

namespace App\Cms\Definitions;

class SettingsMail extends SettingsForUser
{
    public $title = 'Почта';

    public function getFilterScope($collection)
    {
        return $collection->filter('mail');
    }
}
