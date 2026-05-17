<?php

namespace App\Livewire\Checkout;

use App\Models\DeliveryPickupPoint;
use App\Models\DeliveryWarehouse;
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

    // Адреса (для кур'єра)
    public string $address = '';

    // Пункт видачі (для pickup)
    public ?int $pickupPointId = null;

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

    public function updatedAddress(): void
    {
        $this->dispatchUpdate();
    }

    private function dispatchUpdate(): void
    {
        $this->dispatch('delivery-details-updated',
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
