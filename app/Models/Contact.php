<?php

namespace App\Models;

class Contact extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [];

    protected $guarded = [];

    /** @return array<array-key, mixed> */
    public function listEmail(): array
    {
        return $this->toArrayList($this->t('email'));
    }

    /** @return array<array-key, mixed> */
    public function listPhone(): array
    {
        return $this->toArrayList($this->t('phone'));
    }

    /** @return array<array-key, mixed> */
    public function toArrayList(string $item): array
    {
        return array_map('trim', explode(',', $item));
    }
}
