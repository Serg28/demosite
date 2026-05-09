<?php

namespace App\Contracts;

interface CarrierAdapter
{
    public function carrier(): string;

    public function label(): string;

    public function syncCities(callable $onProgress): void;

    public function syncWarehouses(callable $onProgress): void;
}
