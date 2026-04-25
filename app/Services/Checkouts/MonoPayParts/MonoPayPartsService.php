<?php

namespace App\Services\Checkouts\MonoPayParts;

use App\Services\Checkouts\MonoPayParts\src\Api\Client;
use App\Services\Checkouts\MonoPayParts\src\Config;
use App\Services\Checkouts\MonoPayParts\src\Mode;

class MonoPayPartsService
{
    private $mode;

    private $config;

    private $client;

    public function __construct(string $callback = '')
    {
        $this->mode = new Mode();
        $this->config = new Config($this->mode, $callback);
    }

    public function validatePhone(string $telephone) {
        $this->client = new Client($this->config);
        return $this->client->validate($telephone);
    }

}
