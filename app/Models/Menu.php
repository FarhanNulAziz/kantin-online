<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'kategori', 'harga', 'stok', 'is_available'])]
class Menu extends Model
{
    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'stok' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
