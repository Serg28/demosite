<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserContact extends Model
{
    /** @use HasFactory<\Database\Factories\UserContactFactory> */
    use HasFactory;

    protected $table = 'user_contacts';

    protected $fillable = [
        'user_id',
        'type',
        'label',
        'first_name',
        'last_name',
        'phone',
        'email',
        'is_primary',
        'info',
    ];

    protected function casts(): array
    {
        return [
            'info'       => 'array',
            'is_primary' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @param Builder<UserContact> $query */
    public function scopeAddresses(Builder $query): Builder
    {
        return $query->where('type', 'address');
    }

    /** @param Builder<UserContact> $query */
    public function scopeRecipients(Builder $query): Builder
    {
        return $query->where('type', 'recipient');
    }

    public function fullName(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function initials(): string
    {
        $first = mb_substr($this->first_name ?? '', 0, 1);
        $last  = mb_substr($this->last_name ?? '', 0, 1);

        return mb_strtoupper($first . $last) ?: '?';
    }

    /**
     * Повертає текстовий рядок для відображення адреси в картці.
     * Підтримує новий формат (city_title + warehouse_title/street/house)
     * та зворотну сумісність зі старим форматом {city, address}.
     */
    public function displayAddress(): string
    {
        $info       = $this->info ?? [];
        $slug       = $info['delivery_slug'] ?? '';
        $required   = config("checkout.delivery_fields.{$slug}.required", []);
        $isAddrType = in_array('street', $required, true);
        $line       = $info['city_title'] ?? ($info['city'] ?? '');

        if ($isAddrType) {
            $parts = array_filter([$info['street'] ?? null, $info['house'] ?? null]);
            if ($parts) {
                $line .= ($line ? ', ' : '') . implode(' ', $parts);
            }
            if (! empty($info['apartment'])) {
                $line .= ', ' . __t('кв.') . ' ' . $info['apartment'];
            }
        } elseif (! empty($info['warehouse_title'])) {
            $line .= ($line ? ', ' : '') . $info['warehouse_title'];
        } elseif (! empty($info['address'])) {
            // Зворотна сумісність: старий формат {city, address}
            $line .= ($line ? ', ' : '') . $info['address'];
        }

        return $line;
    }
}
