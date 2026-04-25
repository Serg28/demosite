<?php

namespace App\Services\Checkouts\MonoPayParts\src\Config\Mode;

use App\Services\Checkouts\MonoPayParts\src\Config\Mode;

final class PreProduction implements Mode
{
    const API_URL = 'https://u2-ext.mono.st4g3.com';

    const API_STORE_ID = 'COMFY';

    const API_SECRET = 'sign_key';

    public function getStoreId(): string
    {
        return self::API_STORE_ID;
    }

    public function getApiUrl(): string
    {
        return self::API_URL;
    }

    public function getSecret(): string
    {
        return self::API_SECRET;
    }
}
