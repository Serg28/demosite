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

    protected function messages(): array
    {
        return [
            'label.max' => __t('Мітка задовга'),
            'phone.max' => __t('Номер телефону занадто довгий'),
        ];
    }

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
    public function fillFromContact(UserContact $contact): void
    {
        $this->label      = $contact->label  ?? '';
        $this->phone      = $contact->phone  ?? '';
        $this->is_primary = (bool) $contact->is_primary;
    }

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

    public function update(UserContact $contact, array $locationData): void
    {
        $this->validate();

        if ($this->is_primary) {
            $contact->user->addresses()
                ->where('id', '!=', $contact->id)
                ->update(['is_primary' => false]);
        }

        $contact->update([
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
