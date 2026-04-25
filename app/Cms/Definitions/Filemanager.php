<?php

namespace App\Cms\Definitions;

use Vis\Builder\Definitions\Resource;

class Filemanager extends Resource
{
    public $title = 'Файловый менеджер';

    public function getList()
    {
        return view(
            'cms.filemanager',

        );
    }
}
