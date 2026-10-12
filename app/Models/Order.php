<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id',
    'order_type',
    'payment_method',
    'total_harga',
    'payment_status',
    'order_status',
])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'total_harga' => 'integer',
        ];
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
