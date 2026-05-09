<?php

namespace App\ValueObjects;

readonly class BreadcrumbItem
{
    public function __construct(
        public string $title,
        public string $url = '',
    ) {}
}
