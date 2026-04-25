<?php

namespace Litvin\Redirectmap;

use Litvin\Redirectmap\Models\RedirectMap;
use Maatwebsite\Excel\Concerns\ToModel;

class RedirectImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        if (!$row[0] || !$row[1]) {
            return;
        }
        
        return new RedirectMap([
            'old_link'     => $row[0],
            'new_link'    => $row[1],
            'status' => $row[2] ?? 301,
        ]);
    }
}