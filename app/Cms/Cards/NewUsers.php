<?php

namespace App\Cms\Cards;

use App\Models\User;
use Vis\Builder\Services\Value;

class NewUsers extends Value
{
    public $title = 'Новые пользователи';

    public function calculate()
    {
        return $this->count(User::class);
    }
}
