<?php

namespace App\Livewire\Checkout;

use App\Models\City;
use App\Models\Delivery;
use App\Models\DeliveryPickupPoint;
use App\Models\DeliveryWarehouse;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Вибір деталей доставки: місто + відділення/поштомат/адреса/пункт видачі.
 * Монтується з wire:key="ds-{deliveryId}" → re-mount при зміні доставки.
 */
class DeliverySelector extends Component
{
    public ?int $deliveryId = null;
    public string $deliverySlug = '';

    // Пошук міста
    public string $citySearch = '';
    public ?int $selectedCityId = null;
    public string $selectedCityTitle = '';

    // Пошук відділення / поштомату
    public string $warehouseSearch = '';
    public ?int $selectedWarehouseId = null;
    public string $selectedWarehouseTitle = '';

    // Адреса (для кур'єра)
    public string $address = '';

    // Пункт видачі (для pickup)
    public ?int $pickupPointId = null;

    public function mount(?int $deliveryId = null, string $deliverySlug = ''): void
    {
        $this->deliveryId = $deliveryId;
        $this->deliverySlug = $deliverySlug;
    }

    #[Computed]
    public function citySuggestions(): Collection
    {
        if (mb_strlen($this->citySearch) < 2) {
            return collect();
        }

        $search = addslashes($this->citySearch);

        return City::query()
            ->active()
            ->whereRaw(
                "(JSON_UNQUOTE(JSON_EXTRACT(title, '$.ua')) LIKE ? OR JSON_UNQUOTE(JSON_EXTRACT(title, '$.ru')) LIKE ?)",
                ["%{$search}%", "%{$search}%"],
            )
            ->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.ua')) ASC")
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function warehouseSuggestions(): Collection
    {
        if (! DeliveryWarehouse::hasWarehouseSearch($this->deliverySlug)
            || $this->selectedCityId === null
            || mb_strlen($this->warehouseSearch) < 1
        ) {
            return collect();
        }

        $search = addslashes($this->warehouseSearch);

        return DeliveryWarehouse::query()
            ->active()
            ->forDeliverySlug($this->deliverySlug)
            ->forCity($this->selectedCityId)
            ->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(title, '$.ua')) LIKE ?",
                ["%{$search}%"],
            )
            ->limit(20)
            ->get();
    }

    #[Computed]
    public function pickupPoints(): Collection
    {
        if ($this->deliveryId === null) {
            return collect();
        }

        return DeliveryPickupPoint::query()
            ->where('delivery_id', $this->deliveryId)
            ->where('is_active', 1)
            ->orderBy('priority')
            ->get();
    }

    #[Computed]
    public function hasWarehouseSearch(): bool
    {
        return DeliveryWarehouse::hasWarehouseSearch($this->deliverySlug);
    }

    #[Computed]
    public function isCourierDelivery(): bool
    {
        return $this->deliverySlug === 'courier';
    }

    #[Computed]
    public function isPickupDelivery(): bool
    {
        return $this->deliverySlug === 'pickup';
    }

    #[Computed]
    public function warehouseLabel(): string
    {
        return match ($this->deliverySlug) {
            'np_poshtamat' => 'Поштомат',
            'ukrposhta'    => 'Поштове відділення',
            'justin'       => 'Відділення Justin',
            'meest'        => 'Відділення Meest',
            'rozetka'      => 'Пункт видачі Rozetka',
            default        => 'Відділення',
        };
    }

    public function selectCity(int $cityId, string $title): void
    {
        $this->selectedCityId = $cityId;
        $this->selectedCityTitle = $title;
        $this->citySearch = $title;
        $this->selectedWarehouseId = null;
        $this->selectedWarehouseTitle = '';
        $this->warehouseSearch = '';
        $this->dispatchUpdate();
    }

    public function clearCity(): void
    {
        $this->selectedCityId = null;
        $this->selectedCityTitle = '';
        $this->citySearch = '';
        $this->selectedWarehouseId = null;
        $this->selectedWarehouseTitle = '';
        $this->warehouseSearch = '';
        $this->dispatchUpdate();
    }

    public function selectWarehouse(int $warehouseId, string $title): void
    {
        $this->selectedWarehouseId = $warehouseId;
        $this->selectedWarehouseTitle = $title;
        $this->warehouseSearch = $title;
        $this->dispatchUpdate();
    }

    public function clearWarehouse(): void
    {
        $this->selectedWarehouseId = null;
        $this->selectedWarehouseTitle = '';
        $this->warehouseSearch = '';
        $this->dispatchUpdate();
    }

    public function selectPickupPoint(int $pointId): void
    {
        $this->pickupPointId = $pointId;
        $this->dispatchUpdate();
    }

    public function updatedAddress(): void
    {
        $this->dispatchUpdate();
    }

    private function dispatchUpdate(): void
    {
        $this->dispatch('delivery-details-updated',
            cityId: $this->selectedCityId,
            deliveryWarehouseId: $this->hasWarehouseSearch ? $this->selectedWarehouseId : null,
            address: $this->address,
            deliveryPickupPointId: $this->isPickupDelivery ? $this->pickupPointId : null,
        );
    }

    public function render(): View
    {
        return view('livewire.checkout.delivery-selector');
    }
}
