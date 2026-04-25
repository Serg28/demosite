<?php

namespace App\Cms\Cards;

use App\Models\User;
use Vis\Builder\Services\Value;

class AvgUsers extends Value
{
    public string $title = 'Average performance';

    public function calculate(): float
    {
        return $this->avg(User::class, 'id');
    }
}
