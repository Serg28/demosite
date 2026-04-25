<?php

namespace App\Cms\Cards;

use App\Models\User;
use Vis\Builder\Services\Trend;

class ChartUser extends Trend
{
    public $title = 'Кол. новых юзеров';

    public function calculate()
    {
        return $this->countByDays(User::class, 'id');
    }
}
