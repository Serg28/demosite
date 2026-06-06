<?php

namespace App\Livewire\Forms\Profile;

use App\Models\User;
use App\Models\UserContact;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RecipientForm extends Form
{
    #[Validate('nullable|string|max:60')]
    public string $label = '';

    #[Validate('required|string|min:2|max:60')]
    public string $first_name = '';

    #[Validate('required|string|min:2|max:60')]
    public string $last_name = '';

    #[Validate('required|string|min:10|max:20')]
    public string $phone = '';

    #[Validate('nullable|email|max:100')]
    public string $email = '';

    protected function messages(): array
    {
        return [
            'first_name.required' => __t("Вкажіть ім'я"),
            'first_name.min'      => __t("Ім'я занадто коротке"),
            'last_name.required'  => __t('Вкажіть прізвище'),
            'last_name.min'       => __t('Прізвище занадто коротке'),
            'phone.required'      => __t('Вкажіть номер телефону'),
            'phone.min'           => __t('Номер телефону занадто короткий'),
            'email.email'         => __t('Невірний формат email'),
        ];
    }

    public bool $is_primary = false;

    public function fillFromContact(UserContact $contact): void
    {
        $this->label      = $contact->label      ?? '';
        $this->first_name = $contact->first_name ?? '';
        $this->last_name  = $contact->last_name  ?? '';
        $this->phone      = $contact->phone       ?? '';
        $this->email      = $contact->email       ?? '';
        $this->is_primary = (bool) $contact->is_primary;
    }

    public function save(User $user): UserContact
    {
        $this->validate();

        if ($this->is_primary) {
            $user->recipients()->update(['is_primary' => false]);
        }

        return $user->contacts()->create([
            'type'       => 'recipient',
            'label'      => $this->label ?: null,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'phone'      => $this->phone,
            'email'      => $this->email ?: null,
            'is_primary' => $this->is_primary,
        ]);
    }

    public function update(UserContact $contact): void
    {
        $this->validate();

        if ($this->is_primary) {
            $contact->user->recipients()
                ->where('id', '!=', $contact->id)
                ->update(['is_primary' => false]);
        }

        $contact->update([
            'label'      => $this->label ?: null,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'phone'      => $this->phone,
            'email'      => $this->email ?: null,
            'is_primary' => $this->is_primary,
        ]);
    }

    public function reset(...$properties): void
    {
        $this->label      = '';
        $this->first_name = '';
        $this->last_name  = '';
        $this->phone      = '';
        $this->email      = '';
        $this->is_primary = false;

        parent::reset(...$properties);
    }
}
