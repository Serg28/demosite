<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalEntitiesRecipient extends Model
{
    use Traits\HasTranslations;

    protected $table = 'legal_entities_recipients';

    public $timestamps = false;

    protected $guarded = [];

    protected $translatable = ['title', 'sms_detail'];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function paymentCredentials(): HasMany
    {
        return $this->hasMany(PaymentCredential::class, 'legal_entity_id');
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', 1);
    }
}
