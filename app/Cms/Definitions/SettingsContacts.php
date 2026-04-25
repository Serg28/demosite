<?php

namespace App\Cms\Definitions;

class SettingsContacts extends SettingsForUser
{
    public $title = 'Телефоны и emails';

    public function getFilterScope($collection)
    {
        return $collection->filter('contacts');
    }
}
