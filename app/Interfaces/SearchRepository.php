<?php

namespace App\Interfaces;

interface SearchRepository
{
    public function search(string $query = ''): void;
}
