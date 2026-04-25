<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Carbon;

class LastModified
{
    private ?Carbon $updatedAt = null;

    public function set(?Carbon $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function get(): ?Carbon
    {
        return $this->updatedAt;
    }
}
