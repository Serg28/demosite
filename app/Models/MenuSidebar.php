<?php

namespace App\Models;

class MenuSidebar extends MenuBase
{
    protected $table = 'menu_sidebar';

    public function getCacheTags()
    {
        return ['menu_sidebar'];
    }
}
