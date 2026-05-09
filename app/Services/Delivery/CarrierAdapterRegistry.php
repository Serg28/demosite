<?php

namespace App\Services\Delivery;

use App\Contracts\CarrierAdapter;
use InvalidArgumentException;

class CarrierAdapterRegistry
{
    /** @var CarrierAdapter[] */
    private array $adapters = [];

    public function register(CarrierAdapter $adapter): void
    {
        $this->adapters[$adapter->carrier()] = $adapter;
    }

    public function get(string $carrier): CarrierAdapter
    {
        return $this->adapters[$carrier]
            ?? throw new InvalidArgumentException("Carrier adapter [{$carrier}] not registered.");
    }

    /** @return CarrierAdapter[] */
    public function all(): array
    {
        return array_values($this->adapters);
    }

    public function has(string $carrier): bool
    {
        return isset($this->adapters[$carrier]);
    }
}
