<?php

namespace App\Livewire\Profile\Address;

use App\Livewire\Forms\Profile\AddressForm;
use App\Models\Delivery;
use App\Models\UserContact;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Manager extends Component
{
    public AddressForm $form;

    public bool $showForm = false;

    // ── City selection (autocomplete → $wire.selectCity) ────────────────
    public ?int $cityId = null;

    public string $cityTitle = '';

    // ── Delivery selection ───────────────────────────────────────────────
    public ?int $deliveryId = null;

    public string $deliverySlug = '';

    // ── Warehouse selection (autocomplete → $wire.selectWarehouse) ───────
    public ?int $warehouseId = null;

    public string $warehouseTitle = '';

    // ── Courier/address fields (wire:model) ──────────────────────────────
    public string $street = '';

    public string $house = '';

    public string $apartment = '';

    #[Computed]
    public function addresses(): \Illuminate\Database\Eloquent\Collection
    {
        return Auth::user()->addresses()->orderByDesc('is_primary')->orderByDesc('id')->get();
    }

    #[Computed]
    public function deliveries(): Collection
    {
        if ($this->cityId === null) {
            return collect();
        }

        return Delivery::query()->active()->forCity($this->cityId)->get();
    }

    #[Computed]
    public function isAddressDelivery(): bool
    {
        $required = config("checkout.delivery_fields.{$this->deliverySlug}.required", []);

        return in_array('street', $required, true);
    }

    #[Computed]
    public function warehouseLabel(): string
    {
        return config("checkout.warehouse_labels.{$this->deliverySlug}", __t('Відділення'));
    }

    #[Computed]
    public function warehouseSearchUrl(): string
    {
        if ($this->deliverySlug === '' || $this->cityId === null) {
            return '';
        }

        return route('api.v1.checkout.warehouses')
            . '?delivery_slug=' . $this->deliverySlug
            . '&city_id=' . $this->cityId;
    }

    // ── Autocomplete callbacks ───────────────────────────────────────────

    public function selectCity(int $id, string $title): void
    {
        $this->cityId    = $id;
        $this->cityTitle = $title;
        $this->resetDelivery();
    }

    public function clearCity(): void
    {
        $this->cityId    = null;
        $this->cityTitle = '';
        $this->resetDelivery();
    }

    public function selectDelivery(int $id, string $slug): void
    {
        $this->deliveryId   = $id;
        $this->deliverySlug = $slug;
        $this->warehouseId    = null;
        $this->warehouseTitle = '';
        $this->street         = '';
        $this->house          = '';
        $this->apartment      = '';

        unset($this->deliveries, $this->isAddressDelivery, $this->warehouseLabel, $this->warehouseSearchUrl);
    }

    public function selectWarehouse(int $id, string $title): void
    {
        $this->warehouseId    = $id;
        $this->warehouseTitle = $title;
    }

    public function clearWarehouse(): void
    {
        $this->warehouseId    = null;
        $this->warehouseTitle = '';
    }

    // ── CRUD ────────────────────────────────────────────────────────────

    public function add(): void
    {
        $this->validateLocation();
        $this->form->validate();
        $this->form->save(Auth::user(), $this->buildLocationData());
        $this->resetForm();
        $this->dispatch('address-added');
    }

    public function delete(int $id): void
    {
        $contact = UserContact::where('user_id', Auth::id())->addresses()->find($id);
        $contact?->delete();
    }

    public function setPrimary(int $id): void
    {
        $user    = Auth::user();
        $contact = UserContact::where('user_id', $user->id)->addresses()->find($id);

        if (! $contact) {
            return;
        }

        $user->addresses()->update(['is_primary' => false]);
        $contact->update(['is_primary' => true]);
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    private function validateLocation(): void
    {
        $rules = ['cityId' => 'required|integer'];

        if ($this->deliveryId === null) {
            $this->addError('deliveryId', __t('Оберіть спосіб доставки'));
        }

        if ($this->isAddressDelivery) {
            $rules['street'] = 'required|string|max:150';
            $rules['house']  = 'required|string|max:20';
        } elseif ($this->deliveryId !== null && $this->warehouseId === null) {
            $this->addError('warehouseId', __t('Оберіть відділення'));
        }

        $this->validate($rules);
    }

    private function buildLocationData(): array
    {
        return [
            'delivery_id'      => $this->deliveryId,
            'delivery_slug'    => $this->deliverySlug,
            'city_id'          => $this->cityId,
            'city_title'       => $this->cityTitle,
            'warehouse_id'     => $this->warehouseId ?: null,
            'warehouse_title'  => $this->warehouseTitle ?: null,
            'street'           => $this->street ?: null,
            'house'            => $this->house ?: null,
            'apartment'        => $this->apartment ?: null,
        ];
    }

    private function resetDelivery(): void
    {
        $this->deliveryId     = null;
        $this->deliverySlug   = '';
        $this->warehouseId    = null;
        $this->warehouseTitle = '';
        $this->street         = '';
        $this->house          = '';
        $this->apartment      = '';

        unset($this->deliveries, $this->isAddressDelivery, $this->warehouseLabel, $this->warehouseSearchUrl);
    }

    private function resetForm(): void
    {
        $this->form->reset();
        $this->cityId         = null;
        $this->cityTitle      = '';
        $this->deliveryId     = null;
        $this->deliverySlug   = '';
        $this->warehouseId    = null;
        $this->warehouseTitle = '';
        $this->street         = '';
        $this->house          = '';
        $this->apartment      = '';
        $this->showForm       = false;

        unset($this->deliveries, $this->isAddressDelivery, $this->warehouseLabel, $this->warehouseSearchUrl);
    }

    public function render(): View
    {
        return view('livewire.profile.address.manager');
    }
}
