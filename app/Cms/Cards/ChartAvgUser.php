<?php

namespace App\Cms\Cards;

use App\Models\User;
use Vis\Builder\Services\Trend;

class ChartAvgUser extends Trend
{
    public string $title = 'The average new users';

    public $size = 'col-xs-6 col-sm-6 col-md-6 col-lg-6';

    public function calculate(): float
    {
        return $this->avgByDays(User::class, 'id');
    }
}
