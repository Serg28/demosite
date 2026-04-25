<?php

namespace App\Cms\Definitions;

class SettingsInternetMarketing extends SettingsForUser
{
    public $title = 'Интернет маркетинг';

    public function getFilterScope($collection)
    {
        return $collection->filter('internet_marketing');
    }
}
