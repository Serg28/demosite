<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentInvoice extends Model
{
    use HasFactory;

    protected $table = 'payment_invoices';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount'           => 'float',
            'gateway_response' => 'array',
            'paid_at'          => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
