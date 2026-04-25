<?php

namespace App\Models;

class MenuFooter extends MenuBase
{
    protected $table = 'menu_footer';

    public function getCacheTags()
    {
        return ['menu_footer'];
    }
}
