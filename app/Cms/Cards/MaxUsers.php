<?php

namespace App\Cms\Cards;

use App\Models\User;
use Vis\Builder\Services\Value;

class MaxUsers extends Value
{
    public $title = 'Max users';

    public function calculate()
    {
        return $this->max(User::class, 'id');
    }
}
