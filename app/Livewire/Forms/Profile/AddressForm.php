<?php

namespace App\Livewire\Forms\Profile;

use App\Models\User;
use App\Models\UserContact;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AddressForm extends Form
{
    #[Validate('nullable|string|max:60')]
    public string $label = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    public bool $is_primary = false;

    /**
     * @param  array{
     *     delivery_id: int|null,
     *     delivery_slug: string,
     *     city_id: int|null,
     *     city_title: string,
     *     warehouse_id: int|null,
     *     warehouse_title: string,
     *     street: string,
     *     house: string,
     *     apartment: string,
     * }  $locationData
     */
    public function save(User $user, array $locationData): UserContact
    {
        $this->validate();

        if ($this->is_primary) {
            $user->addresses()->update(['is_primary' => false]);
        }

        return $user->contacts()->create([
            'type'       => 'address',
            'label'      => $this->label ?: null,
            'phone'      => $this->phone ?: null,
            'is_primary' => $this->is_primary,
            'info'       => $locationData,
        ]);
    }

    public function reset(...$properties): void
    {
        $this->label      = '';
        $this->phone      = '';
        $this->is_primary = false;

        parent::reset(...$properties);
    }
}
