<?php

namespace App\Models;

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
            'credentials' => 'array',
            'is_default'  => 'boolean',
        ];
    }

    public function payMethod(): BelongsTo
    {
        return $this->belongsTo(PayMethod::class);
    }
}
