<?php

namespace App\Models;

class MenuSeocatalog extends MenuBase
{
    protected $table = 'menu_seocatalog';

    public function getCacheTags()
    {
        return ['menu_seocatalog'];
    }
}
