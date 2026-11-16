<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id',
    'midtrans_order_id',
    'payment_type',
    'transaction_status',
    'snap_token',
    'jumlah',
])]
class Payment extends Model
{
    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
