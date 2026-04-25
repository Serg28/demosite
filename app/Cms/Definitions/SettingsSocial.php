<?php

namespace App\Cms\Definitions;

class SettingsSocial extends SettingsForUser
{
    public $title = 'Социальные сети';

    public function getFilterScope($collection)
    {
        return $collection->filter('social');
    }
}
