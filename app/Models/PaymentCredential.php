<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentCredential extends Model
{
    protected $table = 'payment_credentials';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'credentials'  => 'array',
            'days_of_week' => 'array',
            'is_default'   => 'boolean',
        ];
    }

    public function payMethod(): BelongsTo
    {
        return $this->belongsTo(PayMethod::class);
    }

    public function legalEntity(): BelongsTo
    {
        return $this->belongsTo(LegalEntitiesRecipient::class, 'legal_entity_id');
    }

    public function scopeForDay(Builder $query, int $isoWeekday): Builder
    {
        return $query->whereJsonContains('days_of_week', $isoWeekday);
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', 1);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotNull('credentials');
    }
}
