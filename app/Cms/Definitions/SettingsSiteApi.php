<?php

namespace App\Cms\Definitions;

class SettingsSiteApi extends SettingsCurrency
{
    public $title = 'API сайта';

    public function getFilterScope($collection)
    {
        return $collection->filter('site_api');
    }
}
