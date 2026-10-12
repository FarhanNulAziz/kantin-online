<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'menu_id', 'qty', 'harga_satuan'])]
class OrderItem extends Model
{
    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'harga_satuan' => 'integer',
        ];
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
    public function getSubtotalAttribute(): int
    {
        return $this->qty * $this->harga_satuan;
    }
}
