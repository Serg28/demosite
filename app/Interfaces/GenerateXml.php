<?php

namespace App\Interfaces;

use App\Models\Feed;

interface GenerateXml
{
    public function render(Feed $feed): void;
}
