<?php

namespace App\Livewire\Checkout;

use App\Models\DeliveryPickupPoint;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Sub-форма деталей доставки: відділення/поштомат/адреса/пункт видачі.
 * Місто приходить з батька (CheckoutForm) через prop cityId.
 * Монтується з wire:key="ds-{deliveryId}" → re-mount при зміні доставки.
 */
class DeliverySelector extends Component
{
    public ?int $deliveryId = null;

    public string $deliverySlug = '';

    public ?int $cityId = null;

    // Пошук відділення / поштомату (ID зберігаємо, назву — для відображення)
    public ?int $selectedWarehouseId = null;

    public string $selectedWarehouseTitle = '';

    // Пункт видачі (для pickup)
    public ?int $pickupPointId = null;

    // Кур'єрська доставка — структуровані поля адреси
    public string $street = '';

    public string $house = '';

    public string $apartment = '';

    public string $building = '';

    public string $floor = '';

    public bool $isElevator = false;

    public bool $isLifting = false;

    public function mount(?int $deliveryId = null, string $deliverySlug = '', ?int $cityId = null): void
    {
        $this->deliveryId = $deliveryId;
        $this->deliverySlug = $deliverySlug;
        $this->cityId = $cityId;
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
    public function warehouseLabel(): string
    {
        return match ($this->deliverySlug) {
            'np_poshtamat' => 'Поштомат',
            'ukrposhta' => 'Поштове відділення',
            'justin' => 'Відділення Justin',
            'meest' => 'Відділення Meest',
            'rozetka' => 'Пункт видачі Rozetka',
            default => 'Відділення',
        };
    }

    public function selectWarehouse(int $warehouseId, string $title): void
    {
        $this->selectedWarehouseId = $warehouseId;
        $this->selectedWarehouseTitle = $title;
        $this->dispatchUpdate();
    }

    public function clearWarehouse(): void
    {
        $this->selectedWarehouseId = null;
        $this->selectedWarehouseTitle = '';
        $this->dispatchUpdate();
    }

    public function selectPickupPoint(int $pointId): void
    {
        $this->pickupPointId = $pointId;
        $this->dispatchUpdate();
    }

    public function updatedStreet(): void { $this->dispatchUpdate(); }

    public function updatedHouse(): void { $this->dispatchUpdate(); }

    public function updatedApartment(): void { $this->dispatchUpdate(); }

    public function updatedBuilding(): void { $this->dispatchUpdate(); }

    public function updatedFloor(): void { $this->dispatchUpdate(); }

    public function updatedIsElevator(): void { $this->dispatchUpdate(); }

    public function updatedIsLifting(): void { $this->dispatchUpdate(); }

    private function dispatchUpdate(): void
    {
        $this->dispatch('delivery-details-updated',
            deliveryWarehouseId: $this->selectedWarehouseId,
            street: $this->street,
            house: $this->house,
            apartment: $this->apartment,
            building: $this->building,
            floor: $this->floor,
            isElevator: $this->isElevator,
            isLifting: $this->isLifting,
            deliveryPickupPointId: $this->pickupPointId,
        );
    }

    public function render(): View
    {
        return view('livewire.checkout.delivery-selector');
    }
}
