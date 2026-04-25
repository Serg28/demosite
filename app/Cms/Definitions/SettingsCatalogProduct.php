<?php

namespace App\Cms\Definitions;

class SettingsCatalogProduct extends SettingsForUser
{
    public $title = 'Каталог и товары';

    public function getFilterScope($collection)
    {
        return $collection->filter('catalog_product');
    }
}
