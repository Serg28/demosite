<?php

namespace App\Services\Checkouts\MonoPayParts\src;

use App\Services\Checkouts\MonoPayParts\src\Config\Mode;

class Config
{
    private $callback = null;

    private $mode = null;

    public function __construct(Mode $mode, string $callback)
    {
        $this->mode = $mode;
        $this->callback = $callback;
    }

    final public function getApiUrl(?string $suffix = null): string
    {
        if ($suffix !== null) {
            return sprintf('%s/%s', $this->mode->getApiUrl(), $suffix);
        }

        return $this->mode->getApiUrl();
    }

    final public function getStoreId(): string
    {
        return $this->mode->getStoreId();
    }

    final public function getSecret(): string
    {
        return $this->mode->getSecret();
    }

    public function getCallback(): string
    {
        return $this->callback;
    }

    public function getSignature(string $string): string
    {
        $signature = hash_hmac('sha256', $string, $this->getSecret(), true);
        $signature = base64_encode($signature);

        return $signature;
    }
}
