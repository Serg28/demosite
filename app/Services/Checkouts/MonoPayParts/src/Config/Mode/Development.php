<?php

namespace App\Services\Checkouts\MonoPayParts\src\Config\Mode;

use App\Services\Checkouts\MonoPayParts\src\Config\Mode;

final class Development implements Mode
{
    const API_URL = 'https://u2-demo-ext.mono.st4g3.com';

    const API_STORE_ID = 'test_store_with_confirm';

    const API_SECRET = 'secret_98765432--123-123';

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
