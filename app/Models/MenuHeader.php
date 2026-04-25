<?php

namespace App\Models;

class MenuHeader extends MenuBase
{
    protected $table = 'menu_header';

    public function getCacheTags()
    {
        return ['menu_header'];
    }
}
