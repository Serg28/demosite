<?php

namespace App\Cms\Definitions;

class SettingsMessages extends SettingsForUser
{
    public $title = 'Сервисные сообщения';

    public function getFilterScope($collection)
    {
        return $collection->filter('messages');
    }
}
