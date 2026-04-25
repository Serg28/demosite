<?php

namespace App\Services\Checkouts\MonoPayParts\src;

use App\Services\Checkouts\MonoPayParts\src\Config\Mode\Development;
use App\Services\Checkouts\MonoPayParts\src\Config\Mode\PreProduction;
use App\Services\Checkouts\MonoPayParts\src\Config\Mode\Production;
use App\Services\Checkouts\MonoPayParts\src\Config\Mode as ConfigMode;

class Mode implements ConfigMode
{
    /**
     * @var \Illuminate\Config\Repository
     */
    private $storeId;
    /**
     * @var \Illuminate\Config\Repository
     */
    private $secret;
    /**
     * @var PreProduction
     */
    private $config;

    public function __construct()
    {
        $mode = config('services.mono_pay_parts.mode');
        switch ($mode) {
            case 'Production':
                $this->config = new Production(
                    config('services.mono_pay_parts.store_id'),
                    config('services.mono_pay_parts.secret')
                );
                break;
            case 'Development':
                $this->config = new Development();
                break;
            case 'PreProduction':
                $this->config = new PreProduction();
                break;
        }
    }

    public function getApiUrl(): string
    {
        return $this->config->getApiUrl();
    }

    public function getSecret(): string
    {
        return $this->config->getSecret();
    }

    public function getStoreId(): string
    {
        return $this->config->getStoreId();
    }
}
