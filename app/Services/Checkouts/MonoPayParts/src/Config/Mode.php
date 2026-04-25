<?php

namespace App\Services\Checkouts\MonoPayParts\src\Config;

interface Mode
{
    public function getApiUrl(): string;

    public function getSecret(): string;

    public function getStoreId(): string;
}
