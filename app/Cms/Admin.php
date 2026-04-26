<?php

namespace App\Cms;

use Linecore\Cms\Setting\AdminBase;

class Admin extends AdminBase
{
    public function menu(): array
    {
        return cms_admin_config()->getMenu();
    }
}
