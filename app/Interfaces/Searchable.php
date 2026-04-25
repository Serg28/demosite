<?php

namespace App\Interfaces;

interface Searchable
{
    public function getSearchIndex(): string;
    public function getSearchType(): string;
}
