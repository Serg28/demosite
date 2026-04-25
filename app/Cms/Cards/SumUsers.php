<?php

namespace App\Cms\Cards;

use App\Models\User;
use Vis\Builder\Services\Value;

class SumUsers extends Value
{
    public $title = 'Общее количество';

    public function calculate()
    {
        return $this->sum(User::class, 'id');
    }
}
