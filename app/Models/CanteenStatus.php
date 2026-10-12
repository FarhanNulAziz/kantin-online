<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['status', 'updated_by'])]
class CanteenStatus extends Model
{
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** Ambil satu-satunya baris status kantin (dibuat jika belum ada). */
    public static function current(): self
    {
        return static::firstOrCreate([], ['status' => 'tidak_penuh']);
    }

    public static function isFull(): bool
    {
        return static::current()->status === 'penuh';
    }
}
