<?php

namespace App\Cms\Definitions;

use App\Models\Setting;
use Linecore\Cms\Definitions\Resource;

class Settings extends Resource
{
    public $model = Setting::class;
}
